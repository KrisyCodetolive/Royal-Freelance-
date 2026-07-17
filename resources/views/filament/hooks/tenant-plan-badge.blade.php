{{--
    Nom de l'entreprise (tenant) + plan, dans la navbar. Absent pour le
    super_admin, qui n'appartient à aucun tenant.
--}}
@php
    $user = filament()->auth()->user();
    $tenant = $user?->tenant;
    $plan = $tenant?->currentPlan();
@endphp
@if ($tenant)
    <div class="hidden sm:flex items-center gap-2 ml-4 pl-4 border-l border-gray-200 dark:border-white/10">
        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate max-w-40">
            {{ $tenant->name }}
        </span>
        @if ($plan)
            <x-filament::badge :color="match ($plan->slug) {
                'prestige' => 'warning',
                'starter' => 'info',
                default => 'gray',
            }" size="xs">
                {{ $plan->name }}
            </x-filament::badge>
        @endif
    </div>
@endif
