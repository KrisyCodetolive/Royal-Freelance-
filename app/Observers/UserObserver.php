<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        // Generate subdomain from shop_name for commercials
        if ($user->shop_name && !$user->subdomain) {
            $baseSubdomain = Str::slug($user->shop_name, '-');
            $subdomain = Str::limit($baseSubdomain, 30, '');
            $counter = 1;

            while (User::where('subdomain', $subdomain)->exists()) {
                $suffix = '-' . $counter++;
                $subdomain = Str::limit($baseSubdomain, 30 - strlen($suffix), '') . $suffix;
            }

            $user->subdomain = $subdomain;
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'name' => $user->name,
                'role' => $user->roles->first()?->name,
            ])
            ->log('Utilisateur créé');
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log shop_name change
        if ($user->isDirty('shop_name')) {
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_shop_name' => $user->getOriginal('shop_name'),
                    'new_shop_name' => $user->shop_name,
                ])
                ->log('Nom de boutique modifié');
        }

        // Log subdomain change
        if ($user->isDirty('subdomain')) {
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_subdomain' => $user->getOriginal('subdomain'),
                    'new_subdomain' => $user->subdomain,
                ])
                ->log('Sous-domaine modifié');
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Utilisateur supprimé');
    }
}
