<?php

namespace ShamarKellman\AuthLogger\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Models\AuthLog;
use ShamarKellman\AuthLogger\Notifications\FailedSigninAttempts;

class LogFailedLogin
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
     * @param Failed $event
     * @return void
     * @throws \ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(Failed $event): void
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();

        $position = Location::get($ip);
        $country = $position->countryName ?? optional($position)->countryCode ?? "Unknown";

        $authLog = new AuthLog([
            'ip_address' => $ip,
            'is_successful' => false,
            'location' => $country,
            'user_agent' => $userAgent,
            'event_type' => "FAILED LOGIN",
        ]);

        $user->authentications()->save($authLog);

        $attempts = $user->authentications()->where('event_type', 'FAILED LOGIN')
            ->where('created_at', '<', Carbon::now()->toDateTimeString())
            ->where('created_at', '>', Carbon::now()->subMinutes(config('auth-logger.failed_count_range', 10))->toDateTimeString())
            ->count();

        if ($attempts >= config('auth-logger.number_of_attempts', 5) && config('auth-logger.notify')) {
            $user->notify(new FailedSigninAttempts($authLog));
        }
    }
}
