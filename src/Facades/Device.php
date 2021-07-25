<?php

namespace Williamcruzme\Fcm\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class Device extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'device';
    }

    /**
     * Register the typical notifications routes for an application.
     *
     * @return void
     */
    public static function routes($namespace = '\\williamcruzme\\FCM\\Http\\Controllers')
    {
        if (! str_starts_with('\\', $namespace)) {
            $namespace = "\\$namespace";
        }

        Route::prefix('devices')->namespace($namespace)->group(function () {
            Route::post('/', 'DeviceController@store');
            Route::delete('/', 'DeviceController@destroy');
        });
    }
}
