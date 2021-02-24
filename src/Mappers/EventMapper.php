<?php

namespace ShamarKellman\AuthLogger\Mappers;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use ShamarKellman\AuthLogger\Listeners\LogAuthenticated;
use ShamarKellman\AuthLogger\Listeners\LogAuthenticationAttempt;
use ShamarKellman\AuthLogger\Listeners\LogFailedLogin;
use ShamarKellman\AuthLogger\Listeners\LogLockout;
use ShamarKellman\AuthLogger\Listeners\LogPasswordReset;
use ShamarKellman\AuthLogger\Listeners\LogRegisteredUser;
use ShamarKellman\AuthLogger\Listeners\LogSuccessfulLogin;
use ShamarKellman\AuthLogger\Listeners\LogSuccessfulLogout;

trait EventMapper
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $events = [
        Registered::class => [
            LogRegisteredUser::class,
        ],

        Attempting::class => [
            LogAuthenticationAttempt::class,
        ],

        Authenticated::class => [
            LogAuthenticated::class,
        ],

        Login::class => [
            LogSuccessfulLogin::class,
        ],

        Failed::class => [
            LogFailedLogin::class,
        ],

        Logout::class => [
            LogSuccessfulLogout::class,
        ],

        Lockout::class => [
            LogLockout::class,
        ],

        PasswordReset::class => [
            LogPasswordReset::class,
        ],
    ];
}
