<?php

namespace ShamarKellman\AuthLogger\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Models\AuthLog;
use ShamarKellman\AuthLogger\Notifications\SigninFromNewDevice;

class LogSuccessfulLogin
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
     * @param  Login $event
     * @return void
     * @throws \ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();

        $position = Location::get($ip);
        $country = $position->countryName ?? optional($position)->countryCode ?? "Unknown";

        $known = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->first();

        $authLog = new AuthLog([
            'ip_address' => $ip,
            'is_successful' => true,
            'location' => $country,
            'user_agent' => $userAgent,
            'login_at' => Carbon::now(),
            'event_type' => "LOGIN",
        ]);

        $user->authentications()->save($authLog);

        if (! $known && config('auth-logger.notify')) {
            $user->notify(new SigninFromNewDevice($authLog));
        }
    }
}
