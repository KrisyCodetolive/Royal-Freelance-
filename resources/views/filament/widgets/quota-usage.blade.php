@php
    $usage = $this->getUsage();
    $planName = $this->getPlanName();

    $severityBar = [
        'success' => 'bg-primary-500',
        'warning' => 'bg-warning-500',
        'danger' => 'bg-danger-500',
        'unlimited' => 'bg-gray-300 dark:bg-white/20',
    ];

    $severityText = [
        'success' => 'text-gray-500 dark:text-gray-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'unlimited' => 'text-gray-500 dark:text-gray-400',
    ];

    $severityIconBg = [
        'success' => 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400',
        'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',
        'danger' => 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400',
        'unlimited' => 'bg-gray-50 text-gray-500 dark:bg-white/5 dark:text-gray-400',
    ];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Utilisation du plan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Plan actuel : <span class="font-medium">{{ $planName ?? 'Aucun abonnement actif' }}</span>
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
                <div class="rounded-xl border border-gray-200 dark:border-white/10 p-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $severityIconBg[$data['severity']] }}">
                            <x-filament::icon :icon="$data['icon']" class="h-4 w-4" />
                        </span>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $data['label'] }}</p>
                    </div>

                    <p class="mt-3 text-xl font-semibold text-gray-950 dark:text-white">
                        {{ $data['used'] }}
                        <span class="text-sm font-normal text-gray-400 dark:text-gray-500">/ {{ $data['limit'] ?? '∞' }}</span>
                    </p>

                    @if(!is_null($data['limit']))
                        <div class="mt-3 w-full h-2 rounded-full bg-gray-100 dark:bg-white/10">
                            <div
                                class="h-2 rounded-full transition-all {{ $severityBar[$data['severity']] }}"
                                style="width: {{ max(4, $data['ratio'] * 100) }}%"
                            ></div>
                        </div>
                        <p class="mt-1.5 text-[11px] font-medium {{ $severityText[$data['severity']] }}">
                            {{ round($data['ratio'] * 100) }}% utilisé
                        </p>
                    @else
                        <p class="mt-1.5 text-[11px] font-medium text-gray-400 dark:text-gray-500">Illimité</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun espace de travail associé.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
