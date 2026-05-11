<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\InstrumentServiceInterface;
use App\Contracts\ReservationServiceInterface;
use App\Services\InstrumentService;
use App\Services\ReservationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InstrumentServiceInterface::class, InstrumentService::class);
        $this->app->bind(ReservationServiceInterface::class, ReservationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
