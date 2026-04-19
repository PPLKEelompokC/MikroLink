<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

Route::view('pusat-bantuan', 'pusat-bantuan')
    ->middleware(['auth', 'verified'])
    ->name('pusat-bantuan');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';