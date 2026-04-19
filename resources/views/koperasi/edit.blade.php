@extends('layouts.app')

@section('title', 'Manage Koperasi Profil')

@section('content')
<nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
    <div class="flex items-center">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
        </a>
    </div>
    <div class="hidden lg:flex items-center gap-8">
        <a href="{{ route('dashboard') }}" class="font-semibold text-[15px] text-gray-500 hover:text-[#e8a838] transition-colors">Dashboard</a>
        <a href="#" class="font-bold text-[15px] text-[#e8a838]">Manage Koperasi</a>
    </div>
</nav>

<div class="w-full max-w-4xl mx-auto px-10 py-12 relative z-10">
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Manajemen Profil Koperasi</h1>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('koperasi.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_koperasi" class="block text-sm font-bold text-gray-700 mb-1">Nama Koperasi</label>
                <input type="text" name="nama_koperasi" id="nama_koperasi" value="{{ old('nama_koperasi', $koperasi->nama_koperasi) }}" required
                    class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                @error('nama_koperasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="alamat" class="block text-sm font-bold text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" required
                    class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('alamat', $koperasi->alamat) }}</textarea>
                @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg mr-4 hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-[#e8a838] text-white font-bold rounded-lg shadow-md hover:bg-[#ffa200] transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <hr class="my-10 border-gray-200">

        <h2 class="text-xl font-bold text-gray-900 mb-6">Penyesuaian Saldo Kas</h2>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6 flex justify-between items-center">
            <div>
                <span class="text-sm font-bold text-gray-600">Saldo Saat Ini</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($koperasi->saldo_kas, 0, ',', '.') }}</div>
            </div>
            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>

        <form action="{{ route('koperasi.adjustCapital') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="amount" class="block text-sm font-bold text-gray-700 mb-1">Jumlah Penyesuaian (Rp)</label>
                <p class="text-xs text-gray-500 mb-2">Gunakan nilai negatif (contoh: -500000) untuk mengurangi saldo.</p>
                <input type="number" name="amount" id="amount" step="0.01" required placeholder="Contoh: 1000000 atau -500000"
                    class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow-md hover:bg-emerald-700 transition-colors">
                    Sesuaikan Saldo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
