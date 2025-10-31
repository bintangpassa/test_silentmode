<?php

namespace App\Http\Controllers\WebSocket;

use App\Http\Controllers\Controller;
use App\Services\TenantConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantConnectionController extends Controller
{
    public function __invoke(Request $request, TenantConnectionService $service): JsonResponse
    {
        $validated = $request->validate([
            'uuid'        => ['nullable', 'uuid'],
            'tenant_name' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $service->register(
            $validated['uuid'] ?? null,
            $validated['tenant_name'] ?? null,
            $request->ip(),
        );

        $reverbApp = collect(config('reverb.apps.apps'))->first();

        return response()->json([
            'tenant' => $result['tenant'],
            'is_new' => $result['is_new'],
            'reverb' => [
                'app_id' => $reverbApp['app_id'] ?? null,
                'app_key' => $reverbApp['key'] ?? null,
                'host' => config('reverb.servers.reverb.host'),
                'port' => config('reverb.servers.reverb.port'),
                'path' => config('reverb.servers.reverb.path'),
                'scheme' => $reverbApp['options']['scheme'] ?? 'https',
            ],
        ]);
    }
}
