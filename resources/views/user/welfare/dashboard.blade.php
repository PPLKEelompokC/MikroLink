@extends('layouts.app')

@section('title', 'Jejak Kesejahteraan Keluarga - MikroLink')

@section('content')
    <main class="w-full max-w-[1200px] mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-5">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-emerald-600">PBI-12</p>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Jejak Kesejahteraan Keluarga</h1>
                <p class="text-gray-500 font-medium max-w-3xl mt-3">Pantau perubahan pendapatan, akses kebutuhan dasar, dan tren skor kesejahteraan keluarga sesuai target SDG 1.</p>
            </div>
            <a href="{{ route('welfare.create') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                Isi Kuesioner
            </a>
        </section>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Jumlah Log</p>
                <p class="text-2xl font-black text-[#013599] mt-2">{{ $summary['total_logs'] }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Skor Terbaru</p>
                <p class="text-2xl font-black text-emerald-600 mt-2">{{ $summary['latest_score'] ?? '-' }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Pendapatan Saat Ini</p>
                <p class="text-2xl font-black text-gray-900 mt-2">
                    {{ $summary['latest_income'] !== null ? 'Rp '.number_format((float) $summary['latest_income'], 0, ',', '.') : '-' }}
                </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Pertumbuhan</p>
                <p class="text-2xl font-black text-amber-600 mt-2">
                    {{ $summary['trend_growth'] !== null ? $summary['trend_growth'].'%' : $summary['income_growth'].'%' }}
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">Riwayat Kuesioner SDG 1</h2>
                <p class="text-sm text-gray-500 mt-1">Data diurutkan dari periode terbaru.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4">Pendapatan</th>
                            <th class="px-6 py-4">Kebutuhan Dasar</th>
                            <th class="px-6 py-4">Tanggungan</th>
                            <th class="px-6 py-4">Skor</th>
                            <th class="px-6 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $log)
                            <tr class="hover:bg-emerald-50/30">
                                <td class="px-6 py-5 text-sm font-bold text-gray-900">{{ $log->period_date->format('d M Y') }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <span class="font-black text-gray-900">Rp {{ number_format((float) $log->income_after, 0, ',', '.') }}</span>
                                    <span class="block text-xs text-gray-400">Sebelum Rp {{ number_format((float) $log->income_before, 0, ',', '.') }} | {{ $log->incomeGrowthPercentage() }}%</span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <span class="block">Pangan: {{ ucfirst($log->food_security_status) }}</span>
                                    <span class="block text-xs text-gray-400">Pendidikan {{ $log->education_access_status }} | Kesehatan {{ $log->health_access_status }}</span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $log->dependents_count }} orang</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-600" style="width: {{ min(100, max(0, $log->welfare_score)) }}%"></div>
                                        </div>
                                        <span class="text-sm font-black text-gray-900">{{ $log->welfare_score }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-500 max-w-md">{{ $log->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-sm font-bold text-gray-400">Belum ada kuesioner kesejahteraan keluarga.</p>
                                    <a href="{{ route('welfare.create') }}" class="inline-flex mt-4 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white hover:bg-emerald-700 transition-colors">Isi Kuesioner Pertama</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
