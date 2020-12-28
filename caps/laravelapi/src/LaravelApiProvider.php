<?php


namespace Caps\LaravelApi;


use Carbon\Laravel\ServiceProvider;

class LaravelApiProvider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigs();
        $this->loadRoutes();
    }


    public function register()
    {

    }


    public function mergeConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/generate_api.php', 'generate_api');
    }

    public function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
    }

}
