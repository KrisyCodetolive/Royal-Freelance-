<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Page;
use App\Models\Event;
use App\Enums\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingApiController extends Controller
{
    /**
     * Track page engagement (time spent + scroll depth)
     */
    public function trackPageEngagement(Request $request)
    {
        try {
            $data = $request->has('data') 
                ? json_decode($request->input('data'), true) 
                : $request->all();

            $leadId = $data['lead_id'] ?? null;
            $pageId = $data['page_id'] ?? null;
            $timeSpent = $data['time_spent_seconds'] ?? 0;
            $scrollDepth = $data['scroll_depth_percentage'] ?? 0;

            if (!$leadId || !$pageId) {
                return response()->json(['error' => 'Missing lead_id or page_id'], 400);
            }

            $lead = Lead::find($leadId);
            $page = Page::find($pageId);

            if (!$lead || !$page) {
                return response()->json(['error' => 'Lead or page not found'], 404);
            }

            // Create or update engagement event
            $event = Event::updateOrCreate(
                [
                    'lead_id' => $leadId,
                    'page_id' => $pageId,
                    'type' => EventType::PAGE_VIEW,
                    'created_at' => now()->format('Y-m-d H:i:s'), // Same minute
                ],
                [
                    'time_spent_seconds' => $timeSpent,
                    'scroll_depth_percentage' => $scrollDepth,
                    'data' => array_merge($event->data ?? [], [
                        'time_spent_seconds' => $timeSpent,
                        'scroll_depth_percentage' => $scrollDepth,
                    ]),
                ]
            );

            return response()->json(['success' => true, 'event_id' => $event->id]);
        } catch (\Exception $e) {
            Log::error('Tracking engagement failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Update lead data (screen resolution, etc.)
     */
    public function updateLeadData(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $screenResolution = $request->input('screen_resolution');

            if (!$leadId) {
                return response()->json(['error' => 'Missing lead_id'], 400);
            }

            $lead = Lead::find($leadId);

            if (!$lead) {
                return response()->json(['error' => 'Lead not found'], 404);
            }

            // Update lead with screen resolution
            if ($screenResolution && !$lead->screen_resolution) {
                $lead->update([
                    'screen_resolution' => $screenResolution,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update lead data failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Track CTA button click (especially "Rejoindre" on last page)
     */
    public function trackCtaClick(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $pageId = $request->input('page_id');
            $buttonId = $request->input('button_id');

            if (!$leadId || !$pageId) {
                return response()->json(['error' => 'Missing lead_id or page_id'], 400);
            }

            $lead = Lead::find($leadId);
            $page = Page::find($pageId);

            if (!$lead || !$page) {
                return response()->json(['error' => 'Lead or page not found'], 404);
            }

            $trackingService = app(\App\Services\TrackingService::class);
            $event = $trackingService->trackCtaClick($lead, $page, $buttonId);

            return response()->json([
                'success' => true,
                'event_id' => $event->id,
                'is_client' => $lead->fresh()->status->value === 'client',
            ]);
        } catch (\Exception $e) {
            Log::error('Track CTA click failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
