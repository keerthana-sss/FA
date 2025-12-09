<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use App\Repositories\TripRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TripFileRepository;
use App\Contracts\TripRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\TripFileRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TripRepositoryInterface::class, TripRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TripFileRepositoryInterface::class,TripFileRepository::class
    );
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
