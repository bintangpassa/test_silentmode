<?php

namespace App\Services;

use App\Models\Tenant;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class TenantConnectionService
{
    /**
     * Register or refresh a tenant connection
     */
    public function register(?string $uuid = null, ?string $tenantName = null, ?string $ip = null, ?string $note = null, ?string $filePath = null): array
    {
        $attributes = [
            'status' => 'online',
            'ip' => $ip,
            'note' => $note,
            'file_path' => $filePath,
        ];

        $now = Date::now();

        $tenant = null;

        if ($uuid) {
            $tenant = Tenant::query()->where('uuid', $uuid)->first();
        }

        if (! $tenant) {
            $tenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $tenantName,
                'first_connected' => $now,
                'last_connected' => $now,
                'timestamp' => $now,
            ] + $attributes);

            return ['tenant' => $tenant, 'is_new' => true];
        }

        $tenant->fill($attributes);
        $tenant->last_connected = $now;
        $tenant->timestamp = $now;
        $tenant->save();

        return ['tenant' => $tenant, 'is_new' => false];
    }

    /**
     * Mark tenant offline when connection is closed.
     */
    public function markOfflineByUuid(string $uuid): void
    {
        $tenant = Tenant::query()->where('uuid', $uuid)->first();

        if (! $tenant) {
            return;
        }

        $tenant->status = 'offline';
        $tenant->timestamp = Date::now();
        $tenant->save();
    }
}
