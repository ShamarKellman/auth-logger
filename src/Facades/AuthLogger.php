<?php

namespace ShamarKellman\AuthLogger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ShamarKellman\AuthLogger\AuthLog
 */
class AuthLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'auth-logger';
    }
}
