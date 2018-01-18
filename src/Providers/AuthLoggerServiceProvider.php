<?php

namespace Shamarkellman\AuthLogger\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Shamarkellman\AuthLogger\Mappers\EventMapper;

class AuthLoggerServiceProvider extends ServiceProvider
{
    use EventMapper;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'auth-logger');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'auth-logger');
        $this->mergeConfigFrom(__DIR__.'/../config/auth-logger.php', 'auth-logger');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'auth-logger-migrations');
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/auth-logger'),
            ], 'auth-logger-views');
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/auth-logger'),
            ], 'auth-logger-translations');
            $this->publishes([
                __DIR__.'/../config/auth-logger.php' => config_path('auth-logger.php'),
            ], 'auth-logger-config');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register the Authentication Log's events.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $events = $this->app->make(Dispatcher::class);
        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }
}
