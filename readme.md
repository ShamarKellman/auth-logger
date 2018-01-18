Auth Logger
=======================

Laravel package to log all authentication events.


Requirements
============

* PHP >= 7.0
* Laravel >= 5.5

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
- [ ] Tests


Credits
=======

* Shamar Kellman

License
=======

Published under MIT License