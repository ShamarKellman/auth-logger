<?php

namespace ShamarKellman\AuthLogger\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ShamarKellman\AuthLogger\Traits\AuthLoggable;

class User extends Authenticatable
{
    use AuthLoggable, Notifiable;

    protected $guarded = [];
}
