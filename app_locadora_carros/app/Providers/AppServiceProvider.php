<?php

namespace App\Providers;

use App\Repositories\CarroRepository;
use App\Repositories\ICarroRepository;
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
        $this->app->bind(ICarroRepository::class, CarroRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
