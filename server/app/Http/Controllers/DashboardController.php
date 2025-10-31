<?php

namespace App\Http\Controllers;

use App\Events\TenantFileUploadRequested;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::query()
            ->orderByDesc('last_connected')
            ->get();

        return view('dashboard', [
            'tenants' => $tenants,
        ]);
    }

    public function requestUpload(Tenant $tenant): JsonResponse
    {
        if (empty($tenant->uuid)) {
            $tenant->uuid = (string) Str::uuid();
            $tenant->save();
        }

        try {
            TenantFileUploadRequested::dispatch($tenant);
            Log::info('Upload request event dispatched', [
                'tenant_uuid' => $tenant->uuid,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch upload request event!!!', [
                'tenant_uuid' => $tenant->uuid,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to dispatch upload request event',
            ], 500);
        }

        return response()->json([
            'message' => 'Upload request dispatched',
            'uuid' => $tenant->uuid,
        ]);
    }
}
