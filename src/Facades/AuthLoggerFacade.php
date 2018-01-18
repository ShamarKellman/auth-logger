<?php

namespace Shamarkellman\AuthLogger\Facades;

use Illuminate\Support\Facades\Facade;

class AuthLoggerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'auth-logger';
    }
}