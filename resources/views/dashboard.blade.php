@extends('layouts.app')

@section('title', 'Dashboard - MikroLink')

@section('content')
    <!-- Top Navbar -->
    <nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
            </a>
        </div>
        <div class="hidden lg:flex items-center gap-8">
            <a href="#" class="font-bold text-[15px] text-[#e8a838]">Dashboard</a>
            @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Manajer Koperasi']))
                <a href="{{ route('koperasi.edit') }}" class="font-bold text-[15px] text-emerald-600 hover:text-emerald-700 transition-colors">Manage Koperasi</a>
            @endif
        </div>
        <div class="flex items-center">
            <!-- User profile circle -->
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner cursor-pointer hover:bg-gray-400 transition-colors">
                @if(auth()->check())
                    {{ auth()->user()->initials() }}
                @else
                    GU
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="w-full max-w-[1400px] mx-auto px-10 py-12 flex flex-col gap-10 relative z-10">
        
        <!-- Header Section -->
        <div class="w-full flex items-center justify-between">
            <div class="max-w-3xl">
                <h1 class="text-[40px] font-bold text-gray-900 leading-tight tracking-tight">
                    Selamat datang, {{ auth()->user()?->name ?? 'Admin' }}! Kelola profil koperasi dan pantau pergerakan modal secara real-time.
                </h1>
            </div>
            <div class="hidden lg:block">
                <img src="{{ asset('images/flying_girl.png') }}" alt="Flying Girl Illustration" class="w-[220px] h-auto opacity-90 object-contain">
            </div>
        </div>

        <!-- Activity Summary (Replicating Mockup exactly but with FR-02 Data) -->
        <div>
            <h2 class="text-[16px] font-bold text-gray-800 mb-4">Ringkasan Aktivitas Hari Ini</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Modal Tersedia Card (Functional) -->
                <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[14px] font-bold text-gray-800">Modal Tersedia</span>
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div class="text-[32px] font-bold text-gray-900 mb-1 tracking-tight">Rp {{ number_format($availableCapital, 0, ',', '.') }}</div>
                    <div class="text-[12px] text-gray-500 mb-4">Likuiditas {{ $likuiditas }}%</div>
                    <div class="flex items-center text-[12px] font-bold text-emerald-500">
                        <span>Stabil</span>
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                </div>

                <!-- Total Transaksi Card (Replaces Skor Kredit visually) -->
                <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col relative overflow-hidden items-center justify-center">
                    <div class="absolute top-6 left-6 text-[14px] font-bold text-gray-800">Total Transaksi</div>
                    <div class="absolute top-6 right-6 text-amber-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="mt-8 relative flex items-center justify-center w-28 h-28">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                            <path class="text-amber-500" stroke-dasharray="{{ min(100, $totalTransaksi * 5) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute text-[24px] font-bold text-gray-900">{{ $totalTransaksi }}</div>
                    </div>
                </div>

                <!-- Update Terakhir Card (Replaces Dampak SDG visually) -->
                <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[14px] font-bold text-gray-800">Status Update</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-[28px] font-bold text-emerald-500 mb-1 tracking-tight truncate">{{ $terakhirDiperbarui }}</div>
                    <div class="text-[12px] text-gray-500 mb-4">Pembaruan Modal Koperasi</div>
                    <div class="flex items-center text-[12px] font-bold text-emerald-500">
                        <span>Aktif</span>
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bottom Grid Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10">
            
            <!-- Cooperative Profile Details Section -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div class="px-3 py-1.5 bg-emerald-50/80 text-emerald-600 text-[12px] font-bold rounded-md">Profil Koperasi</div>
                    @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Manajer Koperasi']))
                    <a href="{{ route('koperasi.edit') }}" class="text-[12px] font-bold text-[#e8a838] bg-orange-50 px-4 py-1.5 rounded-full hover:bg-orange-100 transition-colors">Edit Profil</a>
                    @endif
                </div>
                
                <div class="flex flex-col gap-6 mt-4">
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-1">ID Koperasi</h4>
                        <p class="text-[16px] font-bold text-gray-900">{{ $koperasi->id_koperasi }}</p>
                    </div>
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Koperasi</h4>
                        <p class="text-[16px] font-bold text-gray-900">{{ $koperasi->nama_koperasi }}</p>
                    </div>
                    <div>
                        <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Operasional</h4>
                        <p class="text-[14px] font-medium text-gray-700 leading-relaxed">{{ $koperasi->alamat }}</p>
                    </div>
                </div>
            </div>

            <!-- Table Section (Capital Logs) -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div class="px-3 py-1.5 bg-blue-50/80 text-blue-600 text-[12px] font-bold rounded-md">Riwayat Transaksi Modal</div>
                    <div class="flex gap-2">
                        <button class="text-[12px] font-bold text-gray-500 bg-gray-50 px-4 py-1.5 rounded-full hover:bg-gray-100 border border-gray-200 transition-colors">Export</button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead class="text-gray-900 font-bold border-b border-gray-100">
                            <tr>
                                <th class="pb-3">ID Transaksi</th>
                                <th class="pb-3">Anggota</th>
                                <th class="pb-3">Jenis</th>
                                <th class="pb-3">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 font-medium">
                            @forelse($capitalLogs as $log)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-4">{{ $log->transaction_id }}</td>
                                <td class="py-4">{{ $log->member_name }}</td>
                                <td class="py-4">{{ $log->type }}</td>
                                <td class="py-4 font-bold text-{{ $log->amount < 0 ? 'red' : 'emerald' }}-600">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 italic">Belum ada riwayat transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
