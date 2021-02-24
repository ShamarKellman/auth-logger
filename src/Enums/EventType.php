<?php


namespace ShamarKellman\AuthLogger\Enums;

use DASPRiD\Enum\AbstractEnum;

class EventType extends AbstractEnum
{
    public const FAILED_LOGIN = 'FAILED LOGIN';
    public const REGISTERED = 'REGISTERED';
    public const LOGIN = 'LOGIN';
    public const LOGOUT = 'LOGOUT';
    public const LOCKOUT = 'LOCKOUT';
    public const PASSWORD_RESET = 'PASSWORD RESET';
}
