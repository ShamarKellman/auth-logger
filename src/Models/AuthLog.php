<?php

namespace ShamarKellman\AuthLogger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthLog extends Model
{
    use HasFactory;

    protected $table = 'auth_logs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['authenticatable_id', 'authenticatable_type'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('auth-logger.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('auth-logger.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * Get the authenticatable entity that the authentication log belongs to.
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }
}
