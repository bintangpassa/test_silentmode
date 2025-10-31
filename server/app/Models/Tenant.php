<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'token_hash',
        'name',
        'status',
        'ip',
        'note',
        'first_connected',
        'last_connected',
        'last_download',
        'timestamp',
        'file_path',
    ];

    protected $casts = [
        'first_connected' => 'datetime',
        'last_connected' => 'datetime',
        'last_download' => 'datetime',
        'timestamp' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
