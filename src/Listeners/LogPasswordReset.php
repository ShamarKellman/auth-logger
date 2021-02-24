<?php

namespace ShamarKellman\AuthLogger\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Models\AuthLog;

class LogPasswordReset
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Create the event listener.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param PasswordReset $event
     * @return void
     * @throws \ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();

        $position = Location::get($ip);
        $country = $position->countryName ?? $position->countryCode ?? 'UNKNOWN';

        $authLog = new AuthLog([
            'ip_address' => $ip,
            'is_successful' => true,
            'location' => $country,
            'user_agent' => $userAgent,
            'event_type' => "PASSWORD RESET",
        ]);

        $user->authentications()->save($authLog);
    }
}
