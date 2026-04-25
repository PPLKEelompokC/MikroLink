<?php

use App\Http\Controllers\SavingsDepositController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CommunityDocumentController;
use App\Http\Controllers\KoperasiController;


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

use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'role:Admin Koperasi,Manajer Koperasi'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/koperasi/edit', [KoperasiController::class, 'edit'])->name('koperasi.edit');
    Route::put('/koperasi/update', [KoperasiController::class, 'update'])->name('koperasi.update');
    Route::post('/koperasi/adjust-capital', [KoperasiController::class, 'adjustCapital'])->name('koperasi.adjustCapital');
});


// --- Feature: Validasi Dokumen Komunitas ---

// 1. User Side: Halaman untuk komunitas mengunggah berkas
Route::get('/community/upload', function () {
    return view('community.upload'); // Pastikan file view ini sudah dibuat
})->name('docs.upload.form');

// 2. Action: Proses simpan dokumen yang diunggah
Route::post('/documents/upload', [CommunityDocumentController::class, 'store'])
    ->name('docs.store');


// --- Admin Area (Gunakan prefix 'admin' agar rapi) ---
Route::prefix('admin')->group(function () {
    
    // 3. Admin Side: Halaman daftar semua dokumen yang masuk untuk divalidasi
    Route::get('/documents', [CommunityDocumentController::class, 'index'])
        ->name('admin.docs.index');

    // 4. Action: Update status (Approve/Reject) dokumen
    Route::patch('/documents/{id}/status', [CommunityDocumentController::class, 'updateStatus'])
        ->name('docs.update');
        
});


// --- Livewire / Auth Routes (Bawaan Laravel Breeze/Volt) ---
require __DIR__.'/auth.php';
require __DIR__.'/auth.php';

// --- Feature: Validasi Setoran Simpanan
// Route untuk User Biasa (Anggota Koperasi)
Route::middleware(['auth'])->group(function () {
    Route::get('/simpanan/upload', [SavingsDepositController::class, 'create'])->name('savings.create');
    Route::post('/simpanan/upload', [SavingsDepositController::class, 'store'])->name('savings.store');
});

// Route untuk Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/simpanan', [SavingsDepositController::class, 'index'])->name('admin.savings.index');
    Route::patch('/simpanan/{deposit}/status', [SavingsDepositController::class, 'updateStatus'])->name('admin.savings.update');
});