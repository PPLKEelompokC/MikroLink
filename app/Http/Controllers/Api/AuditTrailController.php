<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditTrailResource;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $auditTrails = AuditTrail::with('user')
            ->latest()
            ->paginate($perPage);

        return AuditTrailResource::collection($auditTrails);
    }
}
