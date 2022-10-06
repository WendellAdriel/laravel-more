<?php

namespace WendellAdriel\LaravelMore;

use Illuminate\Support\ServiceProvider;

class LaravelMoreServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-more.php' => base_path('config/laravel-more.php'),
            ],
            'config'
        );
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-more.php', 'laravel-more');
    }
}
