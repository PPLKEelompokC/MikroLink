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

Route::get('/aspiration', function () {
    return view('aspirationPortal');
})->name('aspirationPortal');

// Rute baru untuk memproses data (Backend)
Route::post('/aspiration/store', [AspirationController::class, 'store'])->name('aspiration.store');

Route::get('/cara-kerja', function () {
    return view('caraKerja');
})->name('caraKerja');

// --- Protected Routes (Must be Logged In) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Settings (Digabung agar tidak duplikat)
    Route::prefix('settings')->group(function () {
        Route::redirect('/', 'settings/profile');
        Volt::route('profile', 'settings.profile')->name('settings.profile');
        Volt::route('password', 'settings.password')->name('settings.password');
        Volt::route('appearance', 'settings.appearance')->name('settings.appearance');
    });

    // --- Fitur Anggota: Simpanan ---
    Route::prefix('simpanan')->group(function () {
        Volt::route('setor', 'simpanan.create-setoran')->name('simpanan.setor');
    });

    // --- Fitur Anggota: Pinjaman ---
    Route::prefix('pinjaman')->group(function () {
        Volt::route('ajukan', 'pinjaman.ajukan-pinjaman')->name('pinjaman.ajukan');
    });

    // --- Fitur Anggota: Komunitas & Dokumen ---
    Route::get('/community/upload', function () {
        return view('community.upload');
    })->name('docs.upload.form');
    
    Route::post('/documents/upload', [CommunityDocumentController::class, 'store'])->name('docs.store');

    // --- Admin & Manager Area (Keamanan Role) ---
    Route::middleware(['role:Admin Koperasi,Manajer Koperasi'])->prefix('admin')->group(function () {
        
        // Manajemen Profil Koperasi
        Route::get('/koperasi/edit', [KoperasiController::class, 'edit'])->name('koperasi.edit');
        Route::put('/koperasi/update', [KoperasiController::class, 'update'])->name('koperasi.update');
        Route::post('/koperasi/adjust-capital', [KoperasiController::class, 'adjustCapital'])->name('koperasi.adjustCapital');

        // Validasi Simpanan
        Volt::route('simpanan/validasi', 'admin.simpanan.validasi-setoran')->name('admin.simpanan.validasi');

        // Validasi Pinjaman
        Volt::route('pinjaman/validasi', 'admin.pinjaman.validasi-pinjaman')->name('admin.pinjaman.validasi');

        // Validasi Dokumen Komunitas
        Route::get('/documents', [CommunityDocumentController::class, 'index'])->name('admin.docs.index');
        Route::patch('/documents/{id}/status', [CommunityDocumentController::class, 'updateStatus'])->name('docs.update');
    });
});

// --- Livewire / Auth Routes ---
require __DIR__.'/auth.php';