<?php

namespace App\Providers;

use App\Events\ExpenseCreated;
use App\Events\ItineraryCreated;
use App\Listeners\SendExpenseEmail;
use App\Listeners\SendItineraryEmail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // When a new expense is added
        ExpenseCreated::class => [
            SendExpenseEmail::class,
        ],

        // When a new itinerary/place is added
        ItineraryCreated::class => [
            SendItineraryEmail::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
