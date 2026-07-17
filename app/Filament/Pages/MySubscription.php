<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

/**
 * Self-service : seul l'Owner du workspace peut gérer son abonnement
 * (royal-leadpro-phase3.html, "4 Rôles" — Owner uniquement pour
 * "Gérer l'abonnement"). Complète SubscriptionResource, réservé au
 * super_admin pour intervenir sur n'importe quel tenant.
 */
class MySubscription extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Mon abonnement';

    protected static ?string $title = 'Mon abonnement';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.my-subscription';

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public function getSubscription(): ?Subscription
    {
        return auth()->user()->tenant?->activeSubscription();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePlan')
                ->label('Changer de plan')
                ->icon(Heroicon::OutlinedArrowPath)
                ->form([
                    Select::make('plan_id')
                        ->label('Plan')
                        ->options(fn () => Plan::where('is_active', true)->pluck('name', 'id'))
                        ->default(fn () => $this->getSubscription()?->plan_id)
                        ->required(),

                    Select::make('cycle')
                        ->label('Cycle')
                        ->options([
                            'monthly' => 'Mensuel',
                            'yearly' => 'Annuel',
                        ])
                        ->default(fn () => $this->getSubscription()?->cycle ?? 'monthly')
                        ->required(),
                ])
                ->modalDescription('Aucune passerelle de paiement pour l\'instant : le changement de plan est appliqué immédiatement.')
                ->action(function (array $data): void {
                    $tenant = auth()->user()->tenant;
                    $plan = Plan::findOrFail($data['plan_id']);

                    $attributes = [
                        'plan_id' => $plan->id,
                        'cycle' => $data['cycle'],
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => Subscription::computeEndsAt($plan, $data['cycle']),
                    ];

                    $subscription = $tenant->activeSubscription();

                    if ($subscription) {
                        $subscription->update($attributes);
                    } else {
                        $tenant->subscriptions()->create($attributes);
                    }

                    Notification::make()
                        ->title('Plan mis à jour')
                        ->body("Vous êtes maintenant sur le plan {$plan->name}.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
