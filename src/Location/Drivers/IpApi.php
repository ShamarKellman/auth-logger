<?php

namespace ShamarKellman\AuthLogger\Location\Drivers;

use Exception;
use Illuminate\Support\Fluent;
use ShamarKellman\AuthLogger\Location\Position;

class IpApi extends Driver
{
    /**
     * {@inheritdoc}
     */
    protected function url($ip): string
    {
        return "http://ip-api.com/json/$ip";
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryName = $location->country;
        $position->countryCode = $location->countryCode;
        $position->regionCode = $location->region;
        $position->regionName = $location->regionName;
        $position->cityName = $location->city;
        $position->zipCode = $location->zip;
        $position->latitude = (string) $location->lat;
        $position->longitude = (string) $location->lon;
        $position->areaCode = $location->region;

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function process($ip)
    {
        try {
            $response = json_decode($this->getUrlContent($this->url($ip)), true);

            return new Fluent($response);
        } catch (Exception $e) {
            return false;
        }
    }
}
