<?php

namespace WendellAdriel\LaravelMore\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelMore extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-more';
    }
}
