<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use App\Clients\GeoLocationClient;
use App\Repositories\TripRepository;
use App\Repositories\UserRepository;
use App\Services\GeoLocationService;
use App\Repositories\ExpenseRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TripFileRepository;
use App\Repositories\ItineraryRepository;
use App\Contracts\TripRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ExpenseRepositoryInterface;
use App\Contracts\TripFileRepositoryInterface;
use App\Contracts\ItineraryRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TripRepositoryInterface::class, TripRepository::class);
        $this->app->bind(TripFileRepositoryInterface::class,TripFileRepository::class);
        $this->app->bind(ItineraryRepositoryInterface::class, ItineraryRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class,ExpenseRepository::class);

        $this->app->singleton(GeoLocationClient::class, fn() => new GeoLocationClient());

        $this->app->singleton(GeoLocationService::class, 
        function ($app) {
            return new GeoLocationService($app->make(GeoLocationClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();

        // Passport::tokensExpireIn(now()->addMinutes(1440));
        // Passport::refreshTokensExpireIn(now()->addMinutes(1440));
    }
}
