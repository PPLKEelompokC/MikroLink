@extends('layouts.app')

@section('title', 'Dashboard - MikroLink')

@section('content')
    <nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
            </a>
        </div>
        <div class="hidden lg:flex items-center gap-8">
            <a href="#" class="font-bold text-[15px] text-[#e8a838]">Dashboard</a>
            @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Manajer Koperasi', 'admin']))
                <a href="{{ route('koperasi.edit') }}" class="font-bold text-[15px] text-emerald-600 hover:text-emerald-700 transition-colors">Manage Koperasi</a>
                {{-- Link Validasi Setoran untuk Admin --}}
                <a href="{{ route('admin.simpanan.validasi') }}"
                    class="font-bold text-[15px] text-orange-600 hover:text-orange-700 transition-colors flex items-center gap-1.5">
                    Validasi Setoran
                    @if(isset($pendingDepositsCount) && $pendingDepositsCount > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-orange-500 text-white text-[10px] font-extrabold rounded-full">
                            {{ $pendingDepositsCount }}
                        </span>
                    @endif
                </a>
                <a href="#aspiration-management" class="font-bold text-[15px] text-blue-600 hover:text-blue-700 transition-colors">Aspirations Portal</a>
            @endif
        </div>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner hover:bg-gray-400 transition-colors">
                    @if(auth()->check())
                        {{ auth()->user()->initials() }}
                    @else
                        GU
                    @endif
                </div>
            </button>
            
            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50" style="display: none;">
                @if(auth()->check())
                    <div class="px-4 py-2 border-b border-gray-50">
                        <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#e8a838] transition-colors">Login</a>
                @endif
            </div>
        </div>
    </nav>

    <div class="w-full max-w-[1400px] mx-auto px-10 py-12 flex flex-col gap-10 relative z-10">
        
        @if(auth()->user()->role === 'user')
            <div class="flex flex-col gap-8">
                <div class="flex items-center justify-between bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold uppercase">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Status Keanggotaan:</span>
                                @if(isset($kycStatus) && $kycStatus === 'VERIFIED')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-extrabold rounded-full border border-emerald-100 uppercase tracking-widest">Verified (KYC)</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-extrabold rounded-full border border-amber-100 uppercase tracking-widest">Pending Verification</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:flex gap-4">
                        <a href="{{ route('simpanan.setor') }}" wire:navigate class="px-6 py-3 bg-emerald-600 text-white font-bold text-sm rounded-2xl hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Setor Simpanan
                        </a>
                        <a href="{{ route('docs.upload.form') }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-2xl hover:bg-gray-50 transition-all">Upload Dokumen</a>
                        <a href="{{ route('aspirationPortal') }}" class="px-6 py-3 bg-blue-600 text-white font-bold text-sm rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Portal Aspirasi</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1 bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center">
                        <h3 class="text-gray-500 font-bold text-sm uppercase tracking-widest mb-6">Indeks Kepercayaan</h3>
                        <div class="relative w-40 h-40 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-100" />
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-blue-600" 
                                    stroke-dasharray="440" 
                                    stroke-dashoffset="{{ 440 - (440 * ($trustScore ?? 0) / 100) }}"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="absolute flex flex-col items-center">
                                <span class="text-4xl font-extrabold text-gray-900">{{ $trustScore ?? 0 }}</span>
                                <span class="text-[10px] font-bold text-gray-400">SCORE</span>
                            </div>
                        </div>
                        <p class="mt-6 text-sm text-gray-400 font-medium leading-relaxed px-4">
                            Kelayakan pembiayaan Anda berdasarkan metrik keaktifan & administrasi.
                        </p>
                    </div>

                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm flex flex-col gap-4">
                            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Simpanan Pokok</p>
                                <p class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($simpananPokok ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm flex flex-col gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Simpanan Wajib</p>
                                <p class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($simpananWajib ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm flex flex-col gap-4">
                            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Simpanan Sukarela</p>
                                <p class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($simpananSukarela ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="md:col-span-3 bg-gradient-to-r from-gray-900 to-gray-800 p-8 rounded-[32px] text-white flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Total Akumulasi Simpanan</p>
                                <p class="text-3xl font-extrabold mt-2">Rp {{ number_format(($simpananPokok ?? 0) + ($simpananWajib ?? 0) + ($simpananSukarela ?? 0), 0, ',', '.') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ SECTION BARU: Riwayat Setoran Terbaru --}}
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-800">Riwayat Setoran Terbaru</h3>
                        </div>
                        <a href="{{ route('simpanan.setor') }}" wire:navigate
                            class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Setor Lagi
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4">Tanggal</th>
                                    <th class="px-8 py-4">Jenis</th>
                                    <th class="px-8 py-4">Nominal</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                    <th class="px-8 py-4">Catatan Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse(auth()->user()->deposits()->latest()->take(5)->get() as $deposit)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <p class="text-sm text-gray-500">{{ $deposit->created_at->translatedFormat('d M Y') }}</p>
                                            <p class="text-xs text-gray-400">{{ $deposit->created_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-8 py-5">
                                            @php
                                                $jenisColor = [
                                                    'POKOK'    => 'bg-indigo-50 text-indigo-600',
                                                    'WAJIB'    => 'bg-blue-50 text-blue-600',
                                                    'SUKARELA' => 'bg-emerald-50 text-emerald-600',
                                                ][$deposit->type] ?? 'bg-gray-50 text-gray-600';
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold {{ $jenisColor }} uppercase tracking-widest">
                                                {{ $deposit->type }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="text-sm font-extrabold text-gray-900">
                                                Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @php
                                                $statusColor = [
                                                    'PENDING'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                                    'APPROVED' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'REJECTED' => 'bg-red-50 text-red-600 border-red-100',
                                                ][$deposit->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold border {{ $statusColor }} uppercase tracking-widest">
                                                {{ $deposit->status }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="text-xs text-gray-500 italic">
                                                {{ $deposit->admin_note ?? '-' }}
                                            </p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic text-sm">
                                            Belum ada riwayat setoran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pelacakan Aspirasi Terbaru --}}
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-12">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="font-bold text-gray-800">Pelacakan Aspirasi Terbaru</h3>
                        <a href="{{ route('aspirationPortal') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4">Tanggal</th>
                                    <th class="px-8 py-4">Subjek</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($userAspirations ?? [] as $asp)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 text-sm text-gray-500">{{ $asp->created_at->translatedFormat('d M Y') }}</td>
                                        <td class="px-8 py-5 text-sm font-bold text-gray-900">{{ $asp->subject }}</td>
                                        <td class="px-8 py-5">
                                            <div class="flex justify-center">
                                                @php
                                                    $colors = ['pending' => 'bg-amber-50 text-amber-600', 'resolved' => 'bg-emerald-50 text-emerald-600', 'rejected' => 'bg-red-50 text-red-600'];
                                                    $color = $colors[strtolower($asp->status)] ?? 'bg-gray-50 text-gray-600';
                                                @endphp
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold border {{ $color }} uppercase tracking-widest">{{ $asp->status }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-8 py-12 text-center text-gray-400 italic text-sm">Belum ada data aspirasi diajukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            <div class="w-full flex items-center justify-between">
                <div class="max-w-3xl">
                    <h1 class="text-[40px] font-bold text-gray-900 leading-tight tracking-tight">
                        Selamat datang, {{ auth()->user()->name }}! Kelola profil koperasi dan pantau pergerakan modal secara real-time.
                    </h1>
                </div>
                <div class="hidden lg:block">
                    <img src="{{ asset('images/flying_girl.png') }}" alt="Flying Girl Illustration" class="w-[220px] h-auto opacity-90 object-contain">
                </div>
            </div>

            <div>
                <h2 class="text-[16px] font-bold text-gray-800 mb-4">Ringkasan Aktivitas Hari Ini</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[14px] font-bold text-gray-800">Modal Tersedia</span>
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div class="text-[32px] font-bold text-gray-900 mb-1 tracking-tight">Rp {{ number_format($availableCapital ?? 0, 0, ',', '.') }}</div>
                        <div class="text-[12px] text-gray-500 mb-4">Likuiditas {{ $likuiditas ?? 0 }}%</div>
                        <div class="flex items-center text-[12px] font-bold text-emerald-500">
                            <span>Stabil</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col relative overflow-hidden items-center justify-center">
                        <div class="absolute top-6 left-6 text-[14px] font-bold text-gray-800">Total Transaksi</div>
                        <div class="absolute top-6 right-6 text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="mt-8 relative flex items-center justify-center w-28 h-28">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                                <path class="text-amber-500" stroke-dasharray="{{ min(100, ($totalTransaksi ?? 0) * 5) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            <div class="absolute text-[24px] font-bold text-gray-900">{{ $totalTransaksi ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[14px] font-bold text-gray-800">Status Update</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="text-[28px] font-bold text-emerald-500 mb-1 tracking-tight truncate">{{ $terakhirDiperbarui ?? '-' }}</div>
                        <div class="text-[12px] text-gray-500 mb-4">Pembaruan Modal Koperasi</div>
                        <div class="flex items-center text-[12px] font-bold text-emerald-500">
                            <span>Aktif</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 relative overflow-hidden">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h2 class="text-[22px] text-gray-900 leading-tight">
                            <span>Peforma</span> <span class="font-bold">Kesehatan Finansial</span>
                        </h2>
                        <p class="text-[16px] text-gray-500 mt-1">Tren Pertumbuhan Omzet Harian</p>
                    </div>
                    <a href="#" id="btn-ajukan-pinjaman" onclick="handleAjukanPinjaman(event)" class="bg-[#e8a838] hover:bg-[#d4952f] text-white text-[13px] font-bold px-5 py-2.5 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 whitespace-nowrap">
                        Ajukan Pinjaman
                    </a>
                </div>
                <div id="financialChart" class="w-full" style="min-height: 320px;"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
                            <p class="text-[16px] font-bold text-gray-900">{{ $koperasi->id_koperasi ?? '-' }}</p>
                        </div>
                        <div class="border-b border-gray-100 pb-4">
                            <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Koperasi</h4>
                            <p class="text-[16px] font-bold text-gray-900">{{ $koperasi->nama_koperasi ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Operasional</h4>
                            <p class="text-[14px] font-medium text-gray-700 leading-relaxed">{{ $koperasi->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <div class="px-3 py-1.5 bg-blue-50/80 text-blue-600 text-[12px] font-bold rounded-md">Riwayat Perubahan Modal</div>
                        <span class="text-[10px] bg-white px-3 py-1 rounded-full border border-gray-200 font-bold text-gray-400">LIHAT SEMUA</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-50">
                                @forelse($capitalLogs ?? [] as $log)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <p class="text-xs font-bold text-black">{{ $log->type }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $log->action_by }}</p>
                                        </td>
                                        <td class="px-8 py-5 text-right font-extrabold text-emerald-600 text-sm">
                                            Rp {{ number_format($log->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-8 py-10 text-center text-gray-400 italic">Belum ada log tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="aspiration-management" class="w-full pb-10">
                <livewire:admin.aspirations />
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function handleAjukanPinjaman(event) {
        event.preventDefault();
        alert('Fitur Ajukan Pinjaman akan segera tersedia.');
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Manajer Koperasi', 'admin']))
            const chartLabels = @json($chartLabels ?? []);
            const omzetData = @json($omzetData ?? []);
            const creditScoreData = @json($creditScoreData ?? []);

            const chartEl = document.getElementById('financialChart');
            if (!chartEl) return;

            if (chartLabels.length === 0) {
                chartEl.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-400 text-sm italic">Belum ada data performa finansial.</div>';
                return;
            }

            const options = {
                series: [
                    { name: 'Omzet', type: 'area', data: omzetData },
                    { name: 'Skor Kredit', type: 'line', data: creditScoreData }
                ],
                chart: {
                    height: 320, type: 'line', fontFamily: "'Plus Jakarta Sans', sans-serif",
                    toolbar: { show: false }, zoom: { enabled: false },
                    dropShadow: { enabled: true, top: 4, left: 0, blur: 8, opacity: 0.12, color: ['#3b82f6', '#f59e0b'] }
                },
                colors: ['#3b82f6', '#f59e0b'],
                fill: { type: ['gradient', 'solid'], gradient: { shade: 'light', type: 'vertical', opacityFrom: 0.35, opacityTo: 0.05 } },
                stroke: { width: [3, 3], curve: 'smooth' },
                xaxis: { categories: chartLabels, labels: { style: { colors: '#9ca3af', fontSize: '12px', fontWeight: 500 } } },
                yaxis: [{ show: false }, { show: false, opposite: true, min: 0, max: 100 }],
                grid: { show: true, borderColor: '#f3f4f6', strokeDashArray: 4 },
                legend: { show: false },
                tooltip: { shared: true, intersect: false },
            };

            const chart = new ApexCharts(chartEl, options);
            chart.render();
        @endif
    });
</script>
@endpush