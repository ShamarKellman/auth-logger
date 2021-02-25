[![Latest Version on Packagist](https://img.shields.io/packagist/v/shamarkellman/auth-logger.svg?style=flat-square)](https://packagist.org/packages/shamarkellman/auth-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/shamarkellman/auth-logger/run-tests?label=tests)](https://github.com/shamarkellman/auth-logger/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/shamarkellman/auth-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/shamarkellman/auth-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/shamarkellman/auth-logger.svg?style=flat-square)](https://packagist.org/packages/shamarkellman/auth-logger)

Auth Logger
=======================

Laravel package to log all authentication events.


Requirements
============

* PHP >= 7.4
* Laravel 7.x, 8.x

Installation
============

```bash 
composer require shamarkellman/auth-logger
```

Publish assets

```bash
php artisan vendor:publish --provider="Shamarkellman\AuthLogger\Providers\AuthLoggerServiceProvider"
````

Run migrations

```bash
php artisan migrate
```

Usage
=====

Add Shamarkellman\AuthLogger\Traits\AuthLoggable trait to User Models
```php
<?php  

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Shamarkellman\AuthLogger\Traits\AuthLoggable;

class User extends Authenticatable
{
    use Notifiable, AuthLoggable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
```

TODO
=======
- [ ] Log authenticated, authenticating
- [x] Tests


Credits
=======

* Shamar Kellman

License
=======

Published under MIT License
