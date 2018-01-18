<?php

namespace Shamarkellman\AuthLogger\Mappers;


use Shamarkellman\AuthLogger\Listeners\LogAuthenticated;
use Shamarkellman\AuthLogger\Listeners\LogAuthenticationAttempt;
use Shamarkellman\AuthLogger\Listeners\LogFailedLogin;
use Shamarkellman\AuthLogger\Listeners\LogLockout;
use Shamarkellman\AuthLogger\Listeners\LogPasswordReset;
use Shamarkellman\AuthLogger\Listeners\LogRegisteredUser;
use Shamarkellman\AuthLogger\Listeners\LogSuccessfulLogin;
use Shamarkellman\AuthLogger\Listeners\LogSuccessfulLogout;

trait EventMapper
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $events = [
        'Illuminate\Auth\Events\Registered' => [
            LogRegisteredUser::class,
        ],

        'Illuminate\Auth\Events\Attempting' => [
            LogAuthenticationAttempt::class,
        ],

        'Illuminate\Auth\Events\Authenticated' => [
            LogAuthenticated::class,
        ],

        'Illuminate\Auth\Events\Login' => [
            LogSuccessfulLogin::class,
        ],

        'Illuminate\Auth\Events\Failed' => [
            LogFailedLogin::class,
        ],

        'Illuminate\Auth\Events\Logout' => [
            LogSuccessfulLogout::class,
        ],

        'Illuminate\Auth\Events\Lockout' => [
            LogLockout::class,
        ],

        'Illuminate\Auth\Events\PasswordReset' => [
            LogPasswordReset::class,
        ],
    ];
}