<?php

namespace ShamarKellman\AuthLogger\Tests;

use Illuminate\Support\Fluent;
use Mockery as m;
use ShamarKellman\AuthLogger\Exceptions\DriverDoesNotExistException;
use ShamarKellman\AuthLogger\Facades\Location;
use ShamarKellman\AuthLogger\Location\Drivers\Driver;
use ShamarKellman\AuthLogger\Location\Drivers\GeoPlugin;
use ShamarKellman\AuthLogger\Location\Drivers\IpApi;
use ShamarKellman\AuthLogger\Location\Drivers\IpInfo;
use ShamarKellman\AuthLogger\Location\Position;

class LocationTest extends TestCase
{
    public function testDriverProcess(): void
    {
        $driver = m::mock(Driver::class);

        Location::setDriver($driver);

        $position = new Position();
        $position->cityName = 'foo';

        $driver
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('process')->once()->andReturn(new Fluent(['foo']))
            ->shouldReceive('hydrate')->once()->andReturn($position);

        self::assertEquals($position, Location::get());
    }

    public function testDriverFallback(): void
    {
        $this->withoutExceptionHandling();
        $fallback = m::mock(Driver::class)
            ->shouldAllowMockingProtectedMethods();

        $fallback->shouldReceive('get')
            ->once()
            ->andReturn(new Position());

        $driver = m::mock(Driver::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $driver->shouldReceive('process')
            ->once()
            ->andReturn(false);

        $driver->fallback($fallback);

        Location::setDriver($driver);

        self::assertInstanceOf(Position::class, Location::get());
    }

    public function testDriverDoesNotExist()
    {
        config()->set('auth-logger.driver', 'Test');

        $this->expectException(DriverDoesNotExistException::class);

        Location::get();
    }

    public function testIpApi()
    {
        $driver = m::mock(IpApi::class)->makePartial();

        $attributes = [
            'country' => 'United States',
            'countryCode' => 'US',
            'region' => 'CA',
            'regionName' => 'California',
            'city' => 'Long Beach',
            'zip' => '55555',
            'lat' => '50',
            'lon' => '50',
        ];

        $driver
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('process')->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        self::assertInstanceOf(Position::class, $position);
        self::assertEquals([
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => 'CA',
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => '55555',
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => 'CA',
            'ip' => '127.0.0.1',
            'driver' => get_class($driver),
        ], $position->toArray());
    }

    public function testGeoPlugin()
    {
        $driver = m::mock(GeoPlugin::class)->makePartial();

        $attributes = [
            'geoplugin_countryCode' => 'US',
            'geoplugin_countryName' => 'United States',
            'geoplugin_regionName' => 'California',
            'geoplugin_regionCode' => 'CA',
            'geoplugin_city' => 'Long Beach',
            'geoplugin_latitude' => '50',
            'geoplugin_longitude' => '50',
            'geoplugin_areaCode' => '555',
        ];

        $driver
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('process')->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => 'CA',
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => null,
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => '555',
            'ip' => '127.0.0.1',
            'driver' => get_class($driver),
        ], $position->toArray());
    }

    public function testIpInfo(): void
    {
        $driver = m::mock(IpInfo::class)->makePartial();

        $attributes = [
            'country' => 'US',
            'region' => 'California',
            'city' => 'Long Beach',
            'postal' => '55555',
            'loc' => '50,50',
        ];

        $driver
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('process')->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'countryName' => null,
            'countryCode' => 'US',
            'regionCode' => null,
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => '55555',
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => null,
            'ip' => '127.0.0.1',
            'driver' => get_class($driver),
        ], $position->toArray());
    }

    public function test_position_is_empty()
    {
        $position = new Position();
        $position->ip = '192.168.1.1';
        $position->driver = 'foo';

        $this->assertTrue($position->isEmpty());

        $position = new Position();
        $position->isoCode = 'foo';
        $this->assertFalse($position->isEmpty());
    }
}
