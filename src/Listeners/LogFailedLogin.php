<?php

namespace Shamarkellman\AuthLogger\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Shamarkellman\AuthLogger\Location\Location;
use Shamarkellman\AuthLogger\Models\AuthLog;
use Shamarkellman\AuthLogger\Notifications\FailedSigninAttempts;

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
     * @throws \Shamarkellman\AuthLogger\Exceptions\DriverDoesNotExistException
     */
    public function handle(Failed $event)
    {
        $user = $event->user;
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
            'event_type' => "FAILED LOGIN",
        ]);

        if($user) {
            $user->authentications()->save($authLog);

            $attempts = $user->authentications()->where('event_type', 'FAILED LOGIN')
                ->where('created_at', '<', Carbon::now()->toDateTimeString())
                ->where('created_at', '>', Carbon::now()->subMinutes(config('auth-logger.failed_count_range', 10))->toDateTimeString())
                ->count();

            if ($attempts >= config('auth-logger.number_of_attempts', 5) && config('auth-logger.notify')) {
                $user->notify(new FailedSigninAttempts($authLog));
            }
        } else {
            $authLog->save();
        }
    }
}
