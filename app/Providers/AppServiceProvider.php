<?php

namespace App\Providers;

use App\Models\Spareparts;
use App\Events\SparepartUpdated;
use App\Observers\SparepartObserver;
use App\Listeners\SyncToDriveListener;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


    }

    /**
     * Daftar event dan listener (LARAVEL 12: BOLEH DI AppServiceProvider!)
     */
    protected $listen = [
        SparepartUpdated::class => [
            SyncToDriveListener::class,
        ],
    ];
}
