@php
    $usage = $this->getUsage();
    $planName = $this->getPlanName();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Utilisation du plan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Plan actuel : {{ $planName ?? 'Aucun abonnement actif' }}
                </p>
            </div>

            @if($this->canManageBilling() && $this->hasAnyQuotaNearLimit())
                <a href="{{ route('filament.admin.pages.my-subscription') }}"
                   class="fi-btn fi-btn-size-sm inline-flex items-center gap-1 rounded-lg bg-warning-600 px-3 py-2 text-xs font-semibold text-white hover:bg-warning-500">
                    Passer à un plan supérieur
                </a>
            @elseif($this->hasAnyQuotaNearLimit())
                <span class="text-xs font-medium text-warning-600 dark:text-warning-400">
                    Limite bientôt atteinte — contactez votre Owner pour upgrader
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($usage as $data)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['label'] }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">
                        {{ $data['used'] }} / {{ $data['limit'] ?? '∞' }}
                    </p>

                    @if(!is_null($data['limit']))
                        <div class="mt-2 w-full h-1.5 rounded-full bg-gray-200 dark:bg-white/10">
                            <div
                                class="h-1.5 rounded-full {{ $data['is_near_limit'] ? 'bg-warning-500' : 'bg-primary-600' }}"
                                style="width: {{ $data['ratio'] * 100 }}%"
                            ></div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun espace de travail associé.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
