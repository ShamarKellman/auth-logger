<?php

namespace ShamarKellman\AuthLogger\Tests;

use Carbon\Carbon;
use Event;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use ShamarKellman\AuthLogger\Enums\EventType;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Models\AuthLog;
use ShamarKellman\AuthLogger\Notifications\FailedSigninAttempts;
use ShamarKellman\AuthLogger\Tests\Models\User;

class AuthLoggerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testAuthLoggerTableCreated(): void
    {
        $auth = AuthLog::factory([ "is_successful" => "1"])->create();

        $this->assertDatabaseHas('auth_logs', Arr::only($auth->toArray(), ['event_type', 'ip', 'location']));
    }

    public function testUserIsLoggedOnLogin(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);

        Auth::login($user);

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '1',
            'authenticatable_id' => $user->id,
            'authenticatable_type' => $user->getMorphClass(),
            'event_type' => EventType::LOGIN,
        ]);
    }

    public function testFailedLoginIsLogged(): void
    {
        User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);

        Auth::attempt(['email' => 'johndoe@mail.com', 'password' => bcrypt('password1')]);

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '0',
            'event_type' => EventType::FAILED_LOGIN,
        ]);
    }

    public function testSuccessfulLogOutLogged(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);

        Auth::login($user);

        Auth::logout();

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '1',
            'authenticatable_id' => $user->id,
            'authenticatable_type' => $user->getMorphClass(),
            'event_type' => EventType::LOGOUT,
        ]);
    }

    public function testGetLastLoginInfo(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);
        $log = AuthLog::factory(['login_at' => Carbon::now()])->make();
        $user->authentications()->save($log);

        $query = $user->lastLoginAt()->lastLoginIp()->lastLoginLocation()->first();

        self::assertEqualsWithDelta(Carbon::parse($log->login_at)->getTimestamp(), Carbon::parse($query->last_Login_at)->getTimestamp(), 5);
        self::assertEquals($log->ip_address, $query->last_login_ip_address);
        self::assertEquals($log->location, $query->last_login_location);
    }

    public function testGetPreviousLoginInfo(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);
        $log = AuthLog::factory(['login_at' => Carbon::now(), 'location' => 'Jordan'])->make();
        $user->authentications()->save($log);
        $log2 = AuthLog::factory(['login_at' => Carbon::now()->addDay(), 'location' => 'Barbados'])->make();
        $user->authentications()->save($log2);

        $query = $user->previousLoginAt()->previousLoginLocation()->first();

        self::assertEqualsWithDelta(Carbon::parse($log->login_at)->getTimestamp(), Carbon::parse($query->previous_Login_at)->getTimestamp(), 5);
        self::assertEquals($log->location, $query->previous_login_location);
    }

    public function testFailedLoginNotificationNoSentWhenDisabled(): void
    {
        Notification::fake();

        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);

        collect(range(0, 5))->each(static function ($index) {
            Auth::attempt(['email' => 'johndoe@mail.com', 'password' => bcrypt("password{$index}")]);
        });

        self::assertEquals(6, AuthLog::where('event_type', EventType::FAILED_LOGIN)->count());
        Notification::assertNotSentTo($user, FailedSigninAttempts::class);
    }

    public function testFailedLoginNotificationSent(): void
    {
        $this->withoutExceptionHandling();
        Notification::fake();
        config()->set('auth-logger.notify', true);

        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);

        collect(range(0, 6))->each(static function ($index) {
            Auth::attempt(['email' => 'johndoe@mail.com', 'password' => bcrypt("password{$index}")]);
        });

        self::assertEquals(7, AuthLog::where('event_type', EventType::FAILED_LOGIN)->count());
        Notification::assertSentTo($user, FailedSigninAttempts::class);

        config()->set('auth-logger.notify', false);
    }

    public function testUserRegisteredIsLogged(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);
        Event::dispatch(new Registered($user));

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '1',
            'authenticatable_id' => $user->id,
            'authenticatable_type' => $user->getMorphClass(),
            'event_type' => EventType::REGISTERED,
        ]);
    }

    public function testPasswordChangeIsLogged(): void
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'johndoe@mail.com', 'password' => bcrypt('password')]);
        Event::dispatch(new PasswordReset($user));

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '1',
            'authenticatable_id' => $user->id,
            'authenticatable_type' => $user->getMorphClass(),
            'event_type' => EventType::PASSWORD_RESET,
        ]);
    }

    public function testLockoutEventIsLogged(): void
    {
        Event::dispatch(new Lockout(request()));

        $this->assertDatabaseHas('auth_logs', [
            'is_successful' => '0',
            'event_type' => EventType::LOCKOUT,
        ]);
    }

    public function testLocationSessionKey(): void
    {
        $location = Location::setSessionKey('local')->get(config('auth-logger.testing.ip'));

        self::assertEquals($location, session('local'));

        $location = Location::get(config('auth-logger.testing.ip'));

        self::assertEquals($location, session('local'));
    }
}
