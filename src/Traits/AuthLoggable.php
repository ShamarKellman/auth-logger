<?php

namespace ShamarKellman\AuthLogger\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use ShamarKellman\AuthLogger\Enums\EventType;
use ShamarKellman\AuthLogger\Models\AuthLog;

trait AuthLoggable
{
    /**
     * Get the entity's authentications.
     */
    public function authentications(): MorphMany
    {
        return $this->morphMany(AuthLog::class, 'authenticatable')->latest('login_at');
    }

    /**
     * The Authentication Log notifications delivery channels.
     *
     * @return array
     */
    public function notifyAuthenticationLogVia(): array
    {
        return ['mail'];
    }

    /**
     * Get the entity's last login at.
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLastLoginAt(Builder $query): Builder
    {
        return $query->addSelect(['last_login_at' => AuthLog::query()->select('login_at')
            ->whereColumn('authenticatable_id', "{$this->table}.id")
            ->where('authenticatable_type', $this->getMorphClass())
            ->where('event_type', EventType::LOGIN)
            ->latest('login_at')
            ->take(1)
        ])->withCasts(['last_login_at' => 'datetime']);
    }

    /**
     * Get the entity's last login ip address.
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLastLoginIp(Builder $query): Builder
    {
        return $query->addSelect(['last_login_ip_address' => AuthLog::query()->select('ip_address')
            ->whereColumn('authenticatable_id', "{$this->table}.id")
            ->where('authenticatable_type', $this->getMorphClass())
            ->where('event_type', EventType::LOGIN)
            ->latest('login_at')
            ->take(1)
        ]);
    }

    /**
     * Get the entity's last login ip address.
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLastLoginLocation(Builder $query): Builder
    {
        return $query->addSelect(['last_login_location' => AuthLog::query()->select('location')
            ->whereColumn('authenticatable_id', "{$this->table}.id")
            ->where('authenticatable_type', $this->getMorphClass())
            ->where('event_type', EventType::LOGIN)
            ->latest('login_at')
            ->take(1)
        ]);
    }

    /**
     * Get the entity's previous login at.
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePreviousLoginAt(Builder $query): Builder
    {
        return $query->addSelect(['previous_login_at' => AuthLog::query()->select('login_at')
            ->whereColumn('authenticatable_id', "{$this->table}.id")
            ->where('authenticatable_type', $this->getMorphClass())
            ->where('event_type', EventType::LOGIN)
            ->latest('login_at')
            ->skip(1)
            ->take(1)
        ])->withCasts(['previous_login_at' => 'datetime']);
    }

    /**
     * Get the entity's previous login at.
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePreviousLoginLocation(Builder $query): Builder
    {
        return $query->addSelect(['previous_login_location' => AuthLog::query()->select('location')
            ->whereColumn('authenticatable_id', "{$this->table}.id")
            ->where('authenticatable_type', $this->getMorphClass())
            ->where('event_type', EventType::LOGIN)
            ->orderBy('login_at', 'desc')
            ->skip(1)
            ->take(1)
        ]);
    }
}
