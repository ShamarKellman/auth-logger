<?php

namespace ShamarKellman\AuthLogger\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ShamarKellman\AuthLogger\Enums\EventType;
use ShamarKellman\AuthLogger\Models\AuthLog;


class AuthLogFactory extends Factory
{
    protected $model = AuthLog::class;

    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->ipv4,
            'is_successful' => true,
            'event_type' => EventType::LOGIN,
            'user_agent' => $this->faker->userAgent,
            'location' => $this->faker->country,
            'login_at' => $this->faker->dateTime,
            'logout_at' => $this->faker->dateTime,
        ];
    }
}

