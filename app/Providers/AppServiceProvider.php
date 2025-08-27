<?php

namespace App\Providers;

use App\Events\SparepartUpdated;
use App\Listeners\SyncToDriveListener;
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
        //
    }
    protected $listen = [
        SparepartUpdated::class => [
            SyncToDriveListener::class,
        ],
    ];
}
