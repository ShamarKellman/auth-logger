<?php

namespace ShamarKellman\AuthLogger\Location;

use Illuminate\Contracts\Config\Repository;
use ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException;
use ShamarKellman\AuthLogger\Location\Drivers\Driver;

class Location
{
    /**
     * The application configuration.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The session key.
     *
     * @var string
     */
    protected $key = 'location';

    /**
     * The current driver.
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Constructor.
     *
     * @param  Repository  $config
     * @throws DriverDoesNotExistException
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->setDefaultDriver();
    }

    /**
     * Creates the selected driver instance and sets the driver property.
     *
     * @param Driver|mixed $driver
     */
    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * Sets the default driver from the configuration.
     *
     * @throws DriverDoesNotExistException
     */
    public function setDefaultDriver(): void
    {
        $driver = $this->getDriver($this->getDefaultDriver());

        foreach ($this->getDriverFallbacks() as $fallback) {
            $driver->fallback($this->getDriver($fallback));
        }

        $this->setDriver($driver);
    }

    /**
     * Sets the location session key.
     *
     * @param  string  $key
     *
     * @return Location
     */
    public function setSessionKey(string $key): Location
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Retrieve the users location.
     *
     * @param string $ip
     *
     * @return Position|bool
     */
    public function get($ip = '')
    {
        if (session()->has($this->key)) {
            return session($this->key);
        }

        if ($location = $this->driver->get($ip ?: $this->getClientIP())) {
            session([$this->key => $location]);

            return $location;
        }

        return false;
    }

    /**
     * Returns the client IP address. Will return the set config IP if localhost
     * testing is set to true.
     *
     * @return string
     */
    protected function getClientIP(): string
    {
        return $this->localHostTesting() ? $this->getLocalHostTestingIp() : request()->ip();
    }

    /**
     * Retrieves the config option for localhost testing.
     *
     * @return bool
     */
    protected function localHostTesting(): bool
    {
        return config('auth-logger.testing.enabled', false);
    }

    /**
     * Retrieves the config option for the localhost testing IP.
     *
     * @return string
     */
    protected function getLocalHostTestingIp(): string
    {
        return config('auth-logger.testing.ip', '66.102.0.0');
    }

    /**
     * Retrieves the config option for select driver fallbacks.
     *
     * @return array
     */
    protected function getDriverFallbacks(): array
    {
        return config('auth-logger.fallbacks', []);
    }

    /**
     * Returns the selected driver
     *
     * @return string
     */
    protected function getDefaultDriver(): string
    {
        return config('auth-logger.driver');
    }

    /**
     * Returns the specified driver.
     *
     * @param  string  $driver
     *
     * @return Driver
     *
     * @throws DriverDoesNotExistException
     */
    protected function getDriver(string $driver): Driver
    {
        if (class_exists($driver)) {
            return new $driver();
        }

        throw new DriverDoesNotExistException("The driver [{$driver}] does not exist.");
    }
}
