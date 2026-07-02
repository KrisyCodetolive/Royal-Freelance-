@php
    $subscription = $this->getSubscription();
    $plan = $subscription?->plan;
    $usage = $this->getUsage();

    $labels = [
        'tunnels' => 'Tunnels',
        'mailing_lists' => 'Listes mailing',
        'leads' => 'Leads',
        'shared_tunnels' => 'Tunnels partagés',
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
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
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Utilisation</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($usage as $key => $data)
                    <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $labels[$key] }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $data['used'] }} / {{ $data['limit'] ?? '∞' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
