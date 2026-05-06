<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Enums\EventType;
use App\Enums\LeadStatus;
use App\Models\Alert;
use App\Models\Lead;
use App\Models\PushSubscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class AlertService
{
    /**
     * Create an alert for a lead
     */
    public function createAlert(Lead $lead, AlertType $type, ?User $recipient = null, ?string $customMessage = null): Alert
    {
        $alert = Alert::create([
            'tenant_id' => $lead->tenant_id,
            'lead_id' => $lead->id,
            'user_id' => $recipient?->id ?? $lead->assigned_to ?? $lead->brought_by,
            'type' => $type,
            'title' => $type->label(),
            'message' => $customMessage ?? $type->description(),
            'priority' => $type->priority(),
            'data' => [
                'lead_name' => $lead->getDisplayName(),
                'lead_email' => $lead->email,
                'funnel_name' => $lead->funnel?->name,
            ],
        ]);

        // Send notification
        $this->sendNotification($alert);

        return $alert;
    }

    /**
     * Check and create alerts based on lead status change
     */
    public function checkStatusChangeAlerts(Lead $lead, LeadStatus $oldStatus, LeadStatus $newStatus): void
    {
        // Alert when lead becomes HOT
        if (
            !in_array($oldStatus, [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
            && in_array($newStatus, [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
        ) {
            $this->createAlert($lead, AlertType::HOT_LEAD);
        }
    }

    /**
     * Check and create alerts based on event
     */
    public function checkEventAlerts(Lead $lead, EventType $eventType): void
    {
        // Alert on WhatsApp click
        if ($eventType === EventType::WHATSAPP_CLICK) {
            $this->createAlert($lead, AlertType::WHATSAPP_CLICK);
        }

        // Alert for engaged prospect (3 videos in 24h)
        if ($eventType->isVideoEvent()) {
            $videoCount = $lead->events()
                ->whereIn('type', [
                    EventType::VIDEO_50,
                    EventType::VIDEO_75,
                    EventType::VIDEO_100,
                ])
                ->where('created_at', '>=', now()->subHours(24))
                ->count();

            if ($videoCount >= 3) {
                // Check if alert already exists today
                $existingAlert = Alert::where('lead_id', $lead->id)
                    ->where('type', AlertType::ENGAGED)
                    ->where('created_at', '>=', now()->startOfDay())
                    ->exists();

                if (!$existingAlert) {
                    $this->createAlert($lead, AlertType::ENGAGED);
                }
            }
        }
    }

    /**
     * Create new registration alert
     */
    public function createNewRegistrationAlert(Lead $lead): Alert
    {
        return $this->createAlert($lead, AlertType::NEW_REGISTRATION);
    }

    /**
     * Check for inactive leads and create alerts
     */
    public function checkInactiveLeads(Tenant $tenant): int
    {
        $alertsCreated = 0;

        // 7 days inactive
        $inactiveLeads7 = Lead::where('tenant_id', $tenant->id)
            ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
            ->where(function ($q) {
                $q->where('last_activity_at', '<', now()->subDays(7))
                    ->orWhere(function ($q2) {
                        $q2->whereNull('last_activity_at')
                            ->where('created_at', '<', now()->subDays(7));
                    });
            })
            ->where('last_activity_at', '>=', now()->subDays(14))
            ->whereDoesntHave('alerts', function ($q) {
                $q->where('type', AlertType::INACTIVE_7_DAYS)
                    ->where('created_at', '>=', now()->subDays(7));
            })
            ->get();

        foreach ($inactiveLeads7 as $lead) {
            $this->createAlert($lead, AlertType::INACTIVE_7_DAYS);
            $alertsCreated++;
        }

        // 14 days inactive
        $inactiveLeads14 = Lead::where('tenant_id', $tenant->id)
            ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
            ->where(function ($q) {
                $q->where('last_activity_at', '<', now()->subDays(14))
                    ->orWhere(function ($q2) {
                        $q2->whereNull('last_activity_at')
                            ->where('created_at', '<', now()->subDays(14));
                    });
            })
            ->whereDoesntHave('alerts', function ($q) {
                $q->where('type', AlertType::INACTIVE_14_DAYS)
                    ->where('created_at', '>=', now()->subDays(14));
            })
            ->get();

        foreach ($inactiveLeads14 as $lead) {
            $this->createAlert($lead, AlertType::INACTIVE_14_DAYS);
            $alertsCreated++;
        }

        return $alertsCreated;
    }

    /**
     * Send notification for an alert
     */
    public function sendNotification(Alert $alert): void
    {
        $alert->update(['sent_at' => now()]);

        $recipient = $alert->user;
        if (!$recipient) {
            return;
        }

        $this->sendPushNotification($alert, $recipient);
    }

    /**
     * Send Web Push notification to all subscriptions of the user
     */
    public function sendPushNotification(Alert $alert, User $recipient): void
    {
        $subscriptions = PushSubscription::where('user_id', $recipient->id)->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        try {
            // Suppress E_USER_NOTICE/WARNING from minishlink when GMP/BCMath absent
            // The library falls back to a pure-PHP implementation (slower but functional)
            $prev = set_error_handler(static fn() => true);
            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => config('services.vapid.subject'),
                    'publicKey'  => config('services.vapid.public_key'),
                    'privateKey' => config('services.vapid.private_key'),
                ],
            ]);
            set_error_handler($prev);

            $payload = json_encode([
                'title' => $alert->title,
                'body'  => $alert->message,
                'tag'   => 'alert-' . $alert->type->value,
                'url'   => '/commercial/alerts',
            ]);

            foreach ($subscriptions as $sub) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'keys'     => [
                            'p256dh' => $sub->public_key,
                            'auth'   => $sub->auth_token,
                        ],
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
                } elseif (!$report->isSuccess()) {
                    Log::warning('Push notification failed', ['reason' => $report->getReason()]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Push notification skipped: ' . $e->getMessage());
        }
    }

    /**
     * Mark alert as read
     */
    public function markAsRead(Alert $alert): Alert
    {
        $alert->markAsRead();
        return $alert;
    }

    /**
     * Mark all alerts as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread alerts for a user
     */
    public function getUnreadAlerts(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('lead:id,first_name,last_name,email')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get alerts summary for dashboard
     */
    public function getSummary(User $user): array
    {
        $alerts = Alert::where('user_id', $user->id)
            ->where('is_read', false);

        return [
            'total' => (clone $alerts)->count(),
            'hot_leads' => (clone $alerts)->where('type', AlertType::HOT_LEAD)->count(),
            'whatsapp_clicks' => (clone $alerts)->where('type', AlertType::WHATSAPP_CLICK)->count(),
            'inactive' => (clone $alerts)->whereIn('type', [
                AlertType::INACTIVE_7_DAYS,
                AlertType::INACTIVE_14_DAYS,
            ])->count(),
            'new_registrations' => (clone $alerts)->where('type', AlertType::NEW_REGISTRATION)->count(),
        ];
    }
}
