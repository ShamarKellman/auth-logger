<?php

namespace ShamarKellman\AuthLogger\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Models\AuthLog;

class LogSuccessfulLogout
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
     * @param Logout $event
     * @return void
     * @throws \ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        if (isset($user)) {
            $ip = $this->request->ip();

            $position = Location::get($ip);
            $country = $position->countryName ?? optional($position)->countryCode ?? "Unknown";

            $userAgent = $this->request->userAgent();
            $authLog = new AuthLog([
                'ip_address' => $ip,
                'is_successful' => true,
                'location' => $country,
                'user_agent' => $userAgent,
                'logout_at' => Carbon::now(),
                'event_type' => "LOGOUT",
            ]);

            $user->authentications()->save($authLog);
        }
    }
}
