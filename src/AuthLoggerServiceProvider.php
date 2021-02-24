<?php

namespace ShamarKellman\AuthLogger;

use Illuminate\Contracts\Events\Dispatcher;
use ShamarKellman\AuthLogger\Location\Location;
use ShamarKellman\AuthLogger\Mappers\EventMapper;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AuthLoggerServiceProvider extends PackageServiceProvider
{
    use EventMapper;

    public function configurePackage(Package $package): void
    {
        $this->registerEvents();

        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('auth-logger')
            ->hasConfigFile()
            ->hasMigration('create_auth_logs_table');

        $this->app->singleton('location', function ($app) {
            return new Location($app['config']);
        });
    }

    /**
     * Register the Authentication Log's events.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerEvents(): void
    {
        $events = $this->app->make(Dispatcher::class);
        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }
}
