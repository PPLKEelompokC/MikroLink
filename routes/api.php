<?php

use App\Http\Controllers\Api\AuditTrailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('api.audit-trails.index');
});
