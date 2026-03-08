<?php

namespace App\Providers;

use App\Models\Obat;
use App\Observers\ObatObserver;
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
        Obat::observe(ObatObserver::class);
    }
}
