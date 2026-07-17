{{--
    Rôle de l'utilisateur, affiché sous "Profil" dans le menu utilisateur —
    subtil (visible seulement en ouvrant le menu), cohérent avec les badges
    de rôle utilisés ailleurs dans le panel (UsersTable/UserInfolist).
--}}
@php($user = filament()->auth()->user())
@if ($user)
    <div class="px-3 pb-3 -mt-1">
        <x-filament::badge :color="$user->primaryRoleColor()" size="xs">
            {{ $user->primaryRoleLabel() }}
        </x-filament::badge>
    </div>
@endif
