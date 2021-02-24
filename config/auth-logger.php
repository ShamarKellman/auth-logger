<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notify New Device
    |--------------------------------------------------------------------------
    |
    | Here you define whether to receive notifications when logging from a new device.
    |
    */

    'notify' => env('AUTHENTICATION_LOG_NOTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Notify New Device
    |--------------------------------------------------------------------------
    |
    | Here you define the number of time in minutes before the user is notified of suspicious activity.
    |
    */

    'failed_count_range' => 10,

    /*
    |--------------------------------------------------------------------------
    | Notify New Device
    |--------------------------------------------------------------------------
    |
    | Here you define the number of attempts before the user is notified of suspicious activity.
    |
    */

    'number_of_attempts' => 5,

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | The default driver you would like to use for location retrieval.
    |
    */

    'driver' => ShamarKellman\AuthLogger\Location\Drivers\FreeGeoIp::class,

    /*
    |--------------------------------------------------------------------------
    | Driver Fallbacks
    |--------------------------------------------------------------------------
    |
    | The drivers you want to use to retrieve the users location
    | if the above selected driver is unavailable.
    |
    | These will be called upon in order.
    |
    */

    'fallbacks' => [
        ShamarKellman\AuthLogger\Location\Drivers\IpInfo::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Localhost Testing
    |--------------------------------------------------------------------------
    |
    | If your running your website locally and want to test different
    | IP addresses to see location detection, set 'enabled' to true.
    |
    | The testing IP address is a Google host in the United-States.
    |
    */

    'testing' => [

        'enabled' => env('APP_DEBUG', false),

        'ip' => '66.102.0.0',

    ],
];
