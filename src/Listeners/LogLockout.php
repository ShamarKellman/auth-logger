<?php

namespace Shamarkellman\AuthLogger\Listeners;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Shamarkellman\AuthLogger\Location\Location;
use Shamarkellman\AuthLogger\Models\AuthLog;

class LogLockout
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
     * @param Lockout $event
     * @return void
     * @throws \Shamarkellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(Lockout $event)
    {
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();

        $location = new Location();
        $position = $location->get($ip);
        $country = $position->countryName ?? $position->countryCode;

        $authLog = new AuthLog([
            'ip_address' => $ip,
            'is_successful' => false,
            'location' => $country,
            'user_agent' => $userAgent,
            'event_type' => "LOCKOUT",
        ]);

        $authLog->save();
    }
}
