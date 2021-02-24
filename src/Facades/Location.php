<?php


namespace ShamarKellman\AuthLogger\Facades;

use Illuminate\Support\Facades\Facade;
use ShamarKellman\AuthLogger\Location\Drivers\Driver;
use ShamarKellman\AuthLogger\Location\Position;

/**
 * @method static Position|bool get(string $ip = null)
 * @method static \ShamarKellman\AuthLogger\Location\Location setSessionKey(string $key)
 * @method static void setDriver(Driver $driver)
 *
 * @see \ShamarKellman\AuthLogger\Location\Location
 */
class Location extends Facade
{
    /**
     * The IoC key accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'location';
    }
}
