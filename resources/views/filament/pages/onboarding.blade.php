@php
    $user = auth()->user();
    $tenant = $user->tenant;
    $onboarding = app(\App\Services\OnboardingService::class);
    $status = $onboarding->getStatus($tenant);
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Progress Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Configuration de votre espace
                </h2>
                <span class="text-sm text-gray-500">
                    {{ $status['completion_percentage'] }}% complété
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="bg-primary-600 h-3 rounded-full transition-all duration-500"
                    style="width: {{ $status['completion_percentage'] }}%"></div>
            </div>
        </div>

        {{-- Wizard Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            {{ $this->form }}
        </div>

        {{-- Quick Tips --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-primary-50 dark:bg-primary-900/30 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-light-bulb class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    <h3 class="font-medium text-primary-900 dark:text-primary-100">Astuce</h3>
                </div>
                <p class="text-sm text-primary-700 dark:text-primary-300">
                    Choisissez un modèle de tunnel pour démarrer rapidement avec des pages pré-configurées.
                </p>
            </div>

            <div class="bg-success-50 dark:bg-success-900/30 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-success-600 dark:text-success-400" />
                    <h3 class="font-medium text-success-900 dark:text-success-100">Simple</h3>
                </div>
                <p class="text-sm text-success-700 dark:text-success-300">
                    Vous pourrez modifier tous les détails plus tard. L'important est de commencer!
                </p>
            </div>

            <div class="bg-warning-50 dark:bg-warning-900/30 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-users class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                    <h3 class="font-medium text-warning-900 dark:text-warning-100">Équipe</h3>
                </div>
                <p class="text-sm text-warning-700 dark:text-warning-300">
                    Chaque commercial reçoit son propre lien de partage pour tracker ses leads.
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>