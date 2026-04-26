<?php

use App\Http\Controllers\AspirationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommunityDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoperasiController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes (Landing & Auth) ---
Route::get('/', function () {
    return view('landingPage');
})->name('home');

Route::get('/login', function () {
    return view('loginPage');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::get('/register', function () {
    return view('registerPage');
})->name('register');

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/cara-kerja', function () {
    return view('caraKerja');
})->name('caraKerja');

Route::view('pusat-bantuan', 'pusat-bantuan')
    ->middleware(['auth', 'verified'])
    ->name('pusat-bantuan');
    
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('ticketing', [\App\Http\Controllers\TicketController::class, 'index'])->name('ticketing.index');
    Route::get('ticketing/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('ticketing.create');
    Route::post('ticketing', [\App\Http\Controllers\TicketController::class, 'store'])->name('ticketing.store');
    Route::get('ticketing/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('ticketing.show');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Authenticated Routes ---
Route::middleware(['auth'])->group(function () {

    // Dashboard (Akses untuk semua role yang terdaftar)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:Admin Koperasi,Manajer Koperasi,user,admin')
        ->name('dashboard');

    // Portal Aspirasi
    Route::get('/aspiration', [AspirationController::class, 'indexUser'])->name('aspirationPortal');
    Route::post('/aspiration/store', [AspirationController::class, 'store'])->name('aspiration.store');

    // Fitur: Setoran Simpanan (Sisi Anggota)
    Volt::route('/simpanan/setor', 'simpanan.create-setoran')->name('simpanan.setor');

    // Pengaturan Profil (Volt)
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/password', 'settings.password')->name('settings.password');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Koperasi Management (Admin/Manager)
    Route::middleware('role:Admin Koperasi,Manajer Koperasi')->group(function () {
        Route::get('/koperasi/edit', [KoperasiController::class, 'edit'])->name('koperasi.edit');
        Route::put('/koperasi/update', [KoperasiController::class, 'update'])->name('koperasi.update');
        Route::post('/koperasi/adjust-capital', [KoperasiController::class, 'adjustCapital'])->name('koperasi.adjustCapital');
    });

    // Validasi Dokumen Komunitas
    Route::get('/community/upload', function () {
        return view('community.upload');
    })->name('docs.upload.form');

    Route::post('/documents/upload', [CommunityDocumentController::class, 'store'])->name('docs.store');
});

// --- Admin Area (Prefix: /admin) ---
Route::middleware(['auth', 'role:Admin Koperasi,Manajer Koperasi,admin'])
    ->prefix('admin')
    ->name('admin.')  // ← prefix name agar semua route admin punya prefix 'admin.'
    ->group(function () {

        // Validasi Dokumen Komunitas
        Route::get('/documents', [CommunityDocumentController::class, 'index'])->name('docs.index');
        Route::patch('/documents/{id}/status', [CommunityDocumentController::class, 'updateStatus'])->name('docs.update');

        // Fitur: Validasi Setoran Simpanan
        // Mengarah ke resources/views/livewire/admin/validasi-setoran.blade.php
        Volt::route('/simpanan/validasi', 'admin.validasi-setoran')->name('simpanan.validasi');
    });

require __DIR__.'/auth.php';