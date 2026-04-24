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
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#e8a838] transition-colors">Edit Profile</a>
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

        <!-- Performa Kesehatan Finansial Section -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8 relative overflow-hidden">
            <!-- Header -->
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

            <!-- Chart Container -->
            <div id="financialChart" class="w-full" style="min-height: 320px;"></div>
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

@push('scripts')
<script>
    /**
     * Placeholder function for "Ajukan Pinjaman" button.
     * Replace the URL below with the actual route when the loan application page is ready.
     */
    function handleAjukanPinjaman(event) {
        event.preventDefault();
        // TODO: Replace '#' with the actual route, e.g.: window.location.href = '/pinjaman/ajukan';
        window.location.href = "{{ route('pinjaman.ajukan') }}";
    }

    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = @json($chartLabels);
        const omzetData = @json($omzetData);
        const creditScoreData = @json($creditScoreData);
        const omzetPercentage = {{ $omzetPercentage }};
        const latestCreditScore = {{ $latestCreditScore }};

        if (chartLabels.length === 0) {
            document.getElementById('financialChart').innerHTML =
                '<div class="flex items-center justify-center h-64 text-gray-400 text-sm italic">Belum ada data performa finansial.</div>';
            return;
        }

        const options = {
            series: [
                {
                    name: 'Omzet',
                    type: 'area',
                    data: omzetData,
                },
                {
                    name: 'Skor Kredit',
                    type: 'line',
                    data: creditScoreData,
                },
            ],
            chart: {
                height: 320,
                type: 'line',
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                toolbar: { show: false },
                zoom: { enabled: false },
                dropShadow: {
                    enabled: true,
                    top: 4,
                    left: 0,
                    blur: 8,
                    opacity: 0.12,
                    color: ['#3b82f6', '#f59e0b'],
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 1200,
                    animateGradually: { enabled: true, delay: 150 },
                    dynamicAnimation: { enabled: true, speed: 350 },
                },
            },
            colors: ['#3b82f6', '#f59e0b'],
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100],
                },
            },
            stroke: {
                width: [3, 3],
                curve: 'smooth',
            },
            markers: {
                size: [0, 0],
                hover: { sizeOffset: 5 },
                strokeWidth: 3,
                strokeColors: '#fff',
                discrete: [
                    {
                        seriesIndex: 0,
                        dataPointIndex: omzetData.length - 2,
                        fillColor: '#3b82f6',
                        strokeColor: '#fff',
                        size: 6,
                    },
                    {
                        seriesIndex: 1,
                        dataPointIndex: creditScoreData.length - 2,
                        fillColor: '#f59e0b',
                        strokeColor: '#fff',
                        size: 6,
                    },
                ],
            },
            labels: chartLabels,
            xaxis: {
                type: 'category',
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '12px',
                        fontWeight: 500,
                    },
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: [
                {
                    show: false,
                    min: 0,
                },
                {
                    show: false,
                    opposite: true,
                    min: 0,
                    max: 100,
                },
            ],
            grid: {
                show: true,
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } },
                padding: { top: 0, right: 10, bottom: 0, left: 10 },
            },
            legend: { show: false },
            tooltip: {
                shared: false,
                intersect: true,
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const seriesName = w.globals.seriesNames[seriesIndex];
                    let value = '';
                    let bgColor = '';
                    let textColor = '';

                    if (seriesIndex === 0) {
                        // Omzet
                        const pct = series[0][dataPointIndex] > 0
                            ? ((series[0][dataPointIndex] / Math.max(...omzetData)) * 100).toFixed(1)
                            : '0';
                        value = pct + '%';
                        bgColor = '#eff6ff';
                        textColor = '#3b82f6';
                    } else {
                        // Skor Kredit
                        value = series[1][dataPointIndex].toFixed(0) + '%';
                        bgColor = '#fffbeb';
                        textColor = '#f59e0b';
                    }

                    return `<div style="
                        background: ${bgColor};
                        border: 1px solid ${textColor}20;
                        border-radius: 8px;
                        padding: 8px 14px;
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                    ">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">${seriesName}</span>
                            <span style="font-size: 15px; font-weight: 800; color: ${textColor};">${value}</span>
                        </div>
                    </div>`;
                },
            },
            annotations: {
                points: [
                    {
                        x: chartLabels[chartLabels.length - 2],
                        y: omzetData[omzetData.length - 2],
                        seriesIndex: 0,
                        label: {
                            text: 'Omzet  ' + omzetPercentage + '%',
                            borderColor: '#3b82f6',
                            borderWidth: 0,
                            borderRadius: 8,
                            style: {
                                color: '#1e40af',
                                background: '#dbeafe',
                                fontSize: '12px',
                                fontWeight: 700,
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                padding: { left: 10, right: 10, top: 6, bottom: 6 },
                            },
                            offsetY: -15,
                        },
                    },
                    {
                        x: chartLabels[chartLabels.length - 2],
                        y: creditScoreData[creditScoreData.length - 2],
                        yAxisIndex: 1,
                        seriesIndex: 1,
                        label: {
                            text: 'Skor Kredit  ' + latestCreditScore.toFixed(0) + '%',
                            borderColor: '#f59e0b',
                            borderWidth: 0,
                            borderRadius: 8,
                            style: {
                                color: '#92400e',
                                background: '#fef3c7',
                                fontSize: '12px',
                                fontWeight: 700,
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                padding: { left: 10, right: 10, top: 6, bottom: 6 },
                            },
                            offsetY: -15,
                        },
                    },
                ],
            },
        };

        const chart = new ApexCharts(document.getElementById('financialChart'), options);
        chart.render();
    });
</script>
@endpush
