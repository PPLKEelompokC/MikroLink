<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CommunityDocumentController;

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

Route::get('/register', function () {
    return view('registerPage');
})->name('register');

Route::get('/aspiration', function () {
    return view('aspirationPortal');
})->name('aspirationPortal');

Route::get('/cara-kerja', function () {
    return view('caraKerja');
})->name('caraKerja');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// --- Feature: Validasi Dokumen Komunitas (Fullstack) ---

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