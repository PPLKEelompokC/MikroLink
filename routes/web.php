<?php

use App\Http\Controllers\AktaSetoranController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommunityDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoperasiController;
use App\Http\Controllers\LiterasiController;
use App\Http\Controllers\SystemRequirementController;
use App\Http\Controllers\TicketController;
use App\Models\CommunityDocument;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
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

// --- Authenticated Routes ---
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,manager,user,super_admin,Admin Koperasi,Manajer Koperasi')
        ->name('dashboard');

    // Portal Aspirasi
    Route::get('/aspiration', [AspirationController::class, 'indexUser'])->name('aspirationPortal');
    Route::post('/aspiration/store', [AspirationController::class, 'store'])->name('aspiration.store');

    // Pusat Bantuan & Ticketing
    Route::view('/pusat-bantuan', 'pusat-bantuan')->name('pusat-bantuan');
    Route::get('/ticketing', [TicketController::class, 'index'])->name('ticketing.index');
    Route::get('/ticketing/create', [TicketController::class, 'create'])->name('ticketing.create');
    Route::post('/ticketing', [TicketController::class, 'store'])->name('ticketing.store');
    Route::get('/ticketing/{ticket}', [TicketController::class, 'show'])->name('ticketing.show');

    // Fitur: Setoran Simpanan (Anggota)
    Volt::route('/simpanan/setor', 'simpanan.create-setoran')->name('simpanan.setor');

    // Fitur: Penarikan Simpanan Sukarela (Anggota)
    Volt::route('/simpanan/tarik', 'simpanan.tarik-simpanan')->name('simpanan.tarik');

    // Fitur: Download Akta Setoran PDF
    Route::get('/simpanan/akta/{id}', [AktaSetoranController::class, 'download'])
        ->name('simpanan.akta.download');

    // Fitur: Pinjaman (Sisi Anggota)
    Volt::route('/pinjaman/ajukan', 'pinjaman.create-pinjaman')->name('pinjaman.ajukan');
    Volt::route('/pinjaman/tracking', 'pinjaman.tracking-pinjaman')->name('pinjaman.tracking');

    // Pengaturan Profil (Volt)
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/password', 'settings.password')->name('settings.password');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Koperasi Management (Admin/Manager)
    Route::middleware('role:admin,manager,super_admin')->group(function () {
        // Route::get('/koperasi/edit', [KoperasiController::class, 'edit'])->name('koperasi.edit');
        Volt::route('/koperasi/edit', 'admin.profile')->name('koperasi.edit');
        Route::put('/koperasi/update', [KoperasiController::class, 'update'])->name('koperasi.update');
        Route::post('/koperasi/adjust-capital', [KoperasiController::class, 'adjustCapital'])->name('koperasi.adjustCapital');
    });

    Route::middleware('role:admin,manager,super_admin,Admin Koperasi,Manajer Koperasi,Super Admin')
        ->prefix('kebutuhan-sistem')
        ->name('system-requirements.')
        ->group(function () {
            Route::get('/', [SystemRequirementController::class, 'index'])->name('index');
            Route::get('/kelayakan', [SystemRequirementController::class, 'eligibility'])->name('eligibility');
            Route::get('/persetujuan', [SystemRequirementController::class, 'approvals'])->name('approvals');
            Route::get('/alokasi-dana', [SystemRequirementController::class, 'allocation'])->name('allocation');
        });

    // Validasi Dokumen Komunitas
    Route::get('/community/upload', function () {
        return view('community.upload');
    })->name('docs.upload.form');

    Route::post('/documents/upload', [CommunityDocumentController::class, 'store'])->name('docs.store');
});
// Literasi Keuangan (Publik, tapi harus login untuk akses penuh)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/literasi', [LiterasiController::class, 'index'])->name('literasi.index');
    Route::get('/literasi/{slug}', [LiterasiController::class, 'show'])->name('literasi.show')->where('slug', '[a-z0-9-]+');
});

// --- Admin Area (Prefix: /admin) ---
Route::middleware(['auth', 'role:admin,manager,super_admin,Admin Koperasi,Manajer Koperasi,Super Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Validasi Dokumen Komunitas
        Route::get('/documents', [CommunityDocumentController::class, 'index'])->name('docs.index');
        Route::patch('/documents/{id}/status', [CommunityDocumentController::class, 'updateStatus'])->name('docs.update');
        Route::get('/documents/{id}/view', function ($id) {
            $doc = CommunityDocument::findOrFail($id);
            if (! Storage::disk('local')->exists($doc->file_path)) {
                abort(404);
            }

            return Storage::disk('local')->response($doc->file_path);
        })->name('docs.view');

        // Validasi Setoran Simpanan
        Volt::route('/simpanan/validasi', 'admin.validasi-setoran')->name('simpanan.validasi');

        // Validasi Penarikan Simpanan
        Volt::route('/simpanan/tarik-validasi', 'admin.validasi-penarikan')->name('simpanan.tarik.validasi');

        // FR-18: AI Strategic Fund Allocation
        Route::prefix('fund-allocation')->name('fund-allocation.')->group(function () {
            Volt::route('/', 'admin.fund-allocation.index')
                ->middleware('role:manager,super_admin')
                ->name('index');

            Volt::route('/{fundAllocation}', 'admin.fund-allocation.show')
                ->middleware('role:manager,super_admin')
                ->name('show');
        });

        // Aspirasi Admin
        Route::patch('/aspiration/{id}/status', [AspirationController::class, 'updateStatus'])->name('aspiration.update');

        // Fitur: Validasi / Tracking Pinjaman (existing)
        Volt::route('/pinjaman/validasi', 'admin.disbursement-tracking')->name('pinjaman.validasi');

        // Fitur: Workflow Persetujuan Berjenjang ✅
        Volt::route('/pinjaman/review-admin', 'admin.review-pinjaman')->name('pinjaman.review');

        // Fitur: Neraca Keuangan Otomatis
        Volt::route('/neraca', 'admin.neraca-keuangan')->name('neraca.index');

        // Khusus Manajer Koperasi
        Volt::route('/pinjaman/review-manajer', 'admin.review-pinjaman-manajer')->name('pinjaman.review.manajer');
    });

// --- Super Admin Area ---
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Volt::route('/audit-trails', 'admin.audit-trails')->name('audit-trails');
    });

require __DIR__.'/auth.php';
