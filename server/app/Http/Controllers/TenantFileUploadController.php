<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TenantFileUploadController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100MB
        ]);

        $file = $validated['file'];

        $path = $file->storeAs(
            'tenant_uploads/'.$tenant->uuid,
            $file->getClientOriginalName()
        );

        if (! $path) {
            throw ValidationException::withMessages([
                'file' => ['Failed to store uploaded file!!'],
            ]);
        }

        $tenant->file_path = $path;
        $tenant->last_download = now();
        $tenant->timestamp = now();
        $tenant->save();

        return response()->json([
            'message' => 'File uploaded successfully :D',
            'path' => $path,
        ]);
    }
}
