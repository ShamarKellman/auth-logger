<?php

namespace ShamarKellman\AuthLogger\Location\Drivers;

use Illuminate\Support\Fluent;
use ShamarKellman\AuthLogger\Location\Position;

abstract class Driver
{
    public const CURL_MAX_TIME = 2;
    public const CURL_CONNECT_TIMEOUT = 2;
    /**
     * The fallback driver.
     *
     * @var Driver
     */
    protected $fallback;

    /**
     * Append a fallback driver to the end of the chain.
     *
     * @param Driver $handler
     */
    public function fallback(Driver $handler): void
    {
        if (is_null($this->fallback)) {
            $this->fallback = $handler;
        } else {
            $this->fallback->fallback($handler);
        }
    }

    /**
     * Handle the driver request.
     *
     * @param  string  $ip
     *
     * @return Position|bool
     */
    public function get(string $ip)
    {
        $location = $this->process($ip);

        $position = new Position();

        if ($location instanceof Fluent && $this->fluentDataIsNotEmpty($location)) {
            $position = $this->hydrate(new Position(), $location);

            $position->ip = $ip;
            $position->driver = get_class($this);
        }

        if (! $position->isEmpty()) {
            return $position;
        }

        return $this->fallback ? $this->fallback->get($ip) : false;
    }

    /**
     * Determine if the given fluent data is not empty.
     *
     * @param Fluent $data
     *
     * @return bool
     */
    protected function fluentDataIsNotEmpty(Fluent $data): bool
    {
        return ! empty(array_filter($data->getAttributes()));
    }

    /**
     * Returns url content as string.
     *
     * @param  string  $url
     *
     * @return mixed
     */
    protected function getUrlContent(string $url)
    {
        $session = curl_init();

        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_TIMEOUT, static::CURL_MAX_TIME);
        curl_setopt($session, CURLOPT_CONNECTTIMEOUT, static::CURL_CONNECT_TIMEOUT);

        $content = curl_exec($session);

        curl_close($session);

        return $content;
    }

    /**
     * Returns the URL to use for querying the current driver.
     *
     * @param $ip
     * @return string
     */
    abstract protected function url(string $ip): string;

    /**
     * Hydrates the position with the given location
     * instance using the drivers array map.
     *
     * @param Position $position
     * @param Fluent   $location
     *
     * @return Position
     */
    abstract protected function hydrate(Position $position, Fluent $location): Position;

    /**
     * Process the specified driver.
     *
     * @param  string  $ip
     *
     * @return Fluent|bool
     */
    abstract protected function process(string $ip);
}
