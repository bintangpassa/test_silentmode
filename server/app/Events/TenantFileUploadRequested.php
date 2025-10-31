<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantFileUploadRequested implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Tenant $tenant)
    {
    }

    public function broadcastOn(): Channel|array
    {
        return new Channel('tenant-events.'.$this->tenant->uuid);
    }

    public function broadcastAs(): string
    {
        return 'tenant-upload-request';
    }

    public function broadcastWith(): array
    {
        $absoluteUploadUrl = route('api.tenants.upload', $this->tenant);

        return [
            'uuid' => $this->tenant->uuid,
            'upload_url' => $absoluteUploadUrl,
            'upload_path' => route('api.tenants.upload', $this->tenant, absolute: false),
            'requested_at' => now()->toIso8601String(),
        ];
    }
}
