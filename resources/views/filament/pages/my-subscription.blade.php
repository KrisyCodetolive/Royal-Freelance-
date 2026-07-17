@php
    $subscription = $this->getSubscription();
    $plan = $subscription?->plan;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Plan actuel</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $plan?->name ?? 'Aucun abonnement actif' }}
                    </h2>
                </div>
                @if($subscription)
                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                        <p>Cycle : {{ $subscription->cycle === 'yearly' ? 'Annuel' : 'Mensuel' }}</p>
                        <p>
                            {{ $subscription->ends_at
                                ? 'Renouvellement le ' . $subscription->ends_at->format('d/m/Y')
                                : 'Sans expiration' }}
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- Mêmes KPI d'utilisation que le widget du tableau de bord --}}
        @livewire(\App\Filament\Widgets\QuotaUsageWidget::class)
    </div>
</x-filament-panels::page>
