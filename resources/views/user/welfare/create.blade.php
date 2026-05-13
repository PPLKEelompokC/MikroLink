@extends('layouts.app')

@section('title', 'Kuesioner SDG 1 - MikroLink')

@section('content')
    <main class="w-full max-w-4xl mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-5">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-emerald-600">PBI-12</p>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Kuesioner Kesejahteraan Keluarga</h1>
                <p class="text-gray-500 font-medium max-w-2xl mt-3">Catat indikator pendapatan dan akses kebutuhan dasar keluarga untuk memantau kontribusi koperasi terhadap target SDG 1.</p>
            </div>
            <a href="{{ route('welfare.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                Lihat Dashboard
            </a>
        </section>

        <form action="{{ route('welfare.store') }}" method="POST" class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
            @csrf

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">Data Periode Pemantauan</h2>
                <p class="text-sm text-gray-500 mt-1">Gunakan data paling baru dari kondisi keluarga anggota.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-2">
                    <label for="period_date" class="text-sm font-bold text-gray-700">Tanggal Periode</label>
                    <input id="period_date" name="period_date" type="date" value="{{ old('period_date', now()->toDateString()) }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    @error('period_date') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="dependents_count" class="text-sm font-bold text-gray-700">Jumlah Tanggungan</label>
                    <input id="dependents_count" name="dependents_count" type="number" min="0" max="30" value="{{ old('dependents_count', 0) }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    @error('dependents_count') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="income_before" class="text-sm font-bold text-gray-700">Pendapatan Sebelum Bantuan</label>
                    <input id="income_before" name="income_before" type="number" min="0" step="1000" value="{{ old('income_before') }}" placeholder="Contoh: 1500000" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    @error('income_before') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="income_after" class="text-sm font-bold text-gray-700">Pendapatan Saat Ini</label>
                    <input id="income_after" name="income_after" type="number" min="0" step="1000" value="{{ old('income_after') }}" placeholder="Contoh: 2100000" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    @error('income_after') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="food_security_status" class="text-sm font-bold text-gray-700">Akses Pangan</label>
                    <select id="food_security_status" name="food_security_status" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <option value="tercukupi" @selected(old('food_security_status') === 'tercukupi')>Tercukupi</option>
                        <option value="rentan" @selected(old('food_security_status') === 'rentan')>Rentan</option>
                        <option value="kurang" @selected(old('food_security_status') === 'kurang')>Kurang</option>
                    </select>
                    @error('food_security_status') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="education_access_status" class="text-sm font-bold text-gray-700">Akses Pendidikan</label>
                    <select id="education_access_status" name="education_access_status" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <option value="baik" @selected(old('education_access_status') === 'baik')>Baik</option>
                        <option value="terbatas" @selected(old('education_access_status') === 'terbatas')>Terbatas</option>
                        <option value="terhambat" @selected(old('education_access_status') === 'terhambat')>Terhambat</option>
                    </select>
                    @error('education_access_status') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="health_access_status" class="text-sm font-bold text-gray-700">Akses Kesehatan</label>
                    <select id="health_access_status" name="health_access_status" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <option value="baik" @selected(old('health_access_status') === 'baik')>Baik</option>
                        <option value="terbatas" @selected(old('health_access_status') === 'terbatas')>Terbatas</option>
                        <option value="terhambat" @selected(old('health_access_status') === 'terhambat')>Terhambat</option>
                    </select>
                    @error('health_access_status') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex flex-col gap-2">
                    <label for="notes" class="text-sm font-bold text-gray-700">Catatan Perubahan</label>
                    <textarea id="notes" name="notes" rows="4" class="rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Contoh: Pendapatan naik setelah modal dipakai untuk menambah stok harian.">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-6 py-5 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    Simpan Kuesioner
                </button>
            </div>
        </form>
    </main>
@endsection
