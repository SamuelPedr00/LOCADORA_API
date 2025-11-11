<?php

namespace App\Providers;

use App\Repositories\CarroRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\ICarroRepository;
use App\Repositories\IClienteRepository;
use App\Repositories\ILocacaoRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\IMarcaRepository;
use App\Repositories\IModeloRepository;
use App\Repositories\LocacaoRepository;
use App\Repositories\MarcaRepository;
use App\Repositories\ModeloRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register()
    {
        $this->app->bind(IMarcaRepository::class, MarcaRepository::class);
        $this->app->bind(ICarroRepository::class, CarroRepository::class);
        $this->app->bind(IClienteRepository::class, ClienteRepository::class);
        $this->app->bind(ILocacaoRepository::class, LocacaoRepository::class);
        $this->app->bind(IModeloRepository::class, ModeloRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
