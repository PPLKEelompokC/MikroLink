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
            <a href="#" class="font-semibold text-[15px] text-gray-500 hover:text-[#e8a838] transition-colors">Loan Services</a>
            <a href="#" class="font-semibold text-[15px] text-gray-500 hover:text-[#e8a838] transition-colors">Verifikasi & Keanggotaan</a>
            <a href="#" class="font-semibold text-[15px] text-gray-500 hover:text-[#e8a838] transition-colors">Report & Analytics</a>
            <a href="#" class="font-semibold text-[15px] text-gray-500 hover:text-[#e8a838] transition-colors">Education Center</a>
            @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Super Admin']))
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
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

                <!-- NPL Alert Card (Keep mockup aesthetic but adapted context) -->
                <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[14px] font-bold text-gray-800">Profil Validitas</span>
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-[32px] font-bold text-emerald-600 mb-1 tracking-tight">100%</div>
                    <div class="text-[12px] text-gray-500 mb-4">{{ $koperasi->nama_koperasi }}</div>
                    <div class="flex items-center text-[12px] font-bold text-emerald-500">
                        <span>Standart</span>
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>

            </div>
        </div>

        <!-- Chart Section (Now functional with Capital Logs concept) -->
        <div class="bg-white rounded-2xl p-8 border border-neutral-200 shadow-sm">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h2 class="text-[22px] text-gray-500"><span class="font-bold text-gray-900">Peforma</span> Finansial Modal</h2>
                    <h3 class="text-[20px] text-gray-500">Tren <span class="font-bold text-gray-900">Pertumbuhan Saldo Kas</span></h3>
                </div>
                @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Super Admin']))
                <a href="{{ route('koperasi.edit') }}" class="px-6 py-3 bg-[#e8a838] hover:bg-[#ffa200] text-white text-[14px] font-bold rounded-lg shadow-lg shadow-orange-200/50 transition-all">
                    Sesuaikan Modal
                </a>
                @endif
            </div>
            
            <div class="h-80 w-full relative border-l border-b border-gray-100">
                <!-- Grid Lines -->
                <div class="absolute inset-0 flex flex-col justify-between">
                    <div class="w-full border-t border-gray-100 border-dashed h-0"></div>
                    <div class="w-full border-t border-gray-100 border-dashed h-0"></div>
                    <div class="w-full border-t border-gray-100 border-dashed h-0"></div>
                    <div class="w-full border-t border-gray-100 border-dashed h-0"></div>
                </div>
                
                <!-- Abstract curves -->
                <svg class="absolute inset-0 w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <!-- Blue Line (Saldo) -->
                    <path d="M0 85 C 20 85, 40 70, 60 50 C 80 30, 90 25, 100 20" fill="none" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round" />
                    <!-- Dot on Blue Line -->
                    <circle cx="80" cy="35" r="1.5" fill="#3b82f6" />
                    <!-- Yellow Line -->
                    <path d="M0 75 C 30 75, 50 80, 70 82 C 85 83, 95 80, 100 77" fill="none" stroke="#f59e0b" stroke-width="1" stroke-linecap="round" />
                    <!-- Dot on Yellow Line -->
                    <circle cx="80" cy="81.5" r="1.5" fill="#f59e0b" />
                </svg>

                <!-- Tooltips Mockup -->
                <div class="absolute right-[15%] top-[30%] bg-gray-100 text-gray-800 text-[10px] font-bold px-3 py-1 rounded-md shadow-sm">
                    Modal <span class="ml-2">Rp {{ number_format($availableCapital / 1000000, 1, ',', '.') }} M</span>
                </div>
                <div class="absolute right-[15%] top-[75%] bg-white border border-gray-200 text-gray-800 text-[10px] font-bold px-3 py-1 rounded-md shadow-sm">
                    Likuiditas <span class="ml-2">{{ $likuiditas }}%</span>
                </div>

                <!-- X-axis labels -->
                <div class="absolute -bottom-8 w-full flex justify-between text-[12px] text-gray-400 font-medium">
                    <span>Jan</span>
                    <span>Mar</span>
                    <span>Mei</span>
                    <span>Jul</span>
                    <span>Sep</span>
                    <span>Nov</span>
                    <span>Des</span>
                </div>
            </div>
        </div>

        <!-- Bottom Grid Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10">
            
            <!-- Table Section (Capital Logs) -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div class="px-3 py-1.5 bg-blue-50/80 text-blue-600 text-[12px] font-bold rounded-md">Riwayat Transaksi Modal</div>
                    <div class="flex gap-2">
                        <button class="text-[12px] font-bold text-[#e8a838] bg-orange-50 px-4 py-1.5 rounded-full hover:bg-orange-100 transition-colors">Filter</button>
                        <button class="text-[12px] font-bold text-gray-500 bg-gray-50 px-4 py-1.5 rounded-full hover:bg-gray-100 border border-gray-200 transition-colors">Export</button>
                    </div>
                </div>
                <h4 class="text-[13px] text-gray-600 font-medium mb-6">Status log penyesuaian modal terbaru</h4>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead class="text-gray-900 font-bold border-b border-gray-100">
                            <tr>
                                <th class="pb-3">ID Transaksi</th>
                                <th class="pb-3">Anggota</th>
                                <th class="pb-3">Jenis</th>
                                <th class="pb-3">Jumlah</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 font-medium">
                            @forelse($capitalLogs as $log)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-4">{{ $log->transaction_id }}</td>
                                <td class="py-4">{{ $log->member_name }}</td>
                                <td class="py-4">{{ $log->type }}</td>
                                <td class="py-4 font-bold text-{{ $log->amount < 0 ? 'red' : 'emerald' }}-600">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                                <td class="py-4">
                                    <span class="px-2.5 py-1 bg-{{ $log->status == 'Disetujui' ? 'amber' : 'blue' }}-100 text-{{ $log->status == 'Disetujui' ? 'amber' : 'blue' }}-700 rounded-full text-[10px] font-bold">{{ $log->status }}</span>
                                </td>
                                <td class="py-4 text-right"><a href="#" class="text-amber-500 hover:text-amber-600 font-bold flex justify-end items-center gap-1">Lihat <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 italic">Belum ada riwayat transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AI Allocation Section -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 flex flex-col">
                <h3 class="text-[18px] font-bold text-gray-900 mb-8">AI Alokasi Dana <span class="text-xs font-normal text-emerald-500 ml-2">(Powered by OpenRouter)</span></h3>
                
                <div class="flex flex-col gap-6">
                    @foreach($aiRecommendations as $name => $data)
                    <div>
                        <div class="flex justify-between text-[13px] font-bold text-gray-800 mb-2.5">
                            <span>{{ $name }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="{{ $data['color'] }} h-full" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10 bg-amber-50/50 border border-amber-100 rounded-xl p-5 flex justify-between items-center">
                    <div>
                        <h4 class="text-[13px] font-bold text-gray-900 mb-1">Rekomendasi AI</h4>
                        <p class="text-[11px] font-medium text-gray-600">{{ $aiInsight }}</p>
                    </div>
                    <button class="bg-amber-500 hover:bg-amber-600 text-white text-[12px] font-bold px-5 py-2.5 rounded-lg shadow-md shadow-amber-200/50 transition-colors">
                        Lihat Detail
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection
