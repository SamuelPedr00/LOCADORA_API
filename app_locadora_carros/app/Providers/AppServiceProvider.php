<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\IMarcaRepository;
use App\Repositories\MarcaRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register()
    {
        $this->app->bind(IMarcaRepository::class, MarcaRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
