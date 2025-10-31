<?php

namespace App\Listeners;

use App\Services\TenantConnectionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Reverb\Events\MessageReceived;

class TenantSocketEventListener
{
    public function __construct(protected TenantConnectionService $service)
    {
    }

    public function handle(MessageReceived $event): void
    {
        $payload = json_decode($event->message, true);

        if (! is_array($payload) || ! isset($payload['event'])) {
            return;
        }

        $eventName = Str::lower($payload['event']);

        if ($eventName !== 'client-tenant-hello') {
            return;
        }

        $data = $payload['data'] ?? [];

        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $tenantName = $data['tenant_name'] ?? null;

        try {
            $this->service->register(
                $tenantName,
                $data['ip'] ?? null,
                $data['note'] ?? null,
                $data['file_path'] ?? null
            );
        } catch (\Throwable $e) {
            Log::error('Failed to register tenant socket connection: '.$e->getMessage());
        }
    }
}
