<?php

namespace App\Providers;

use App\Events\LeadStatusChanged;
use App\Listeners\HandleLeadStatusChange;
use App\Models\Event;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\User;
use App\Observers\EventObserver;
use App\Observers\FunnelObserver;
use App\Observers\LeadObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event as EventFacade;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LeadStatusChanged::class => [
            HandleLeadStatusChange::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register model observers
        Lead::observe(LeadObserver::class);
        Event::observe(EventObserver::class);
        Funnel::observe(FunnelObserver::class);
        User::observe(UserObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
