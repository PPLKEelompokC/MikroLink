@extends('layouts.app')

@section('title', 'AI Alokasi Dana Strategis - MikroLink')

@section('content')
    <main class="w-full max-w-[1400px] mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        @include('system-requirements.partials.navigation')

        <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">AI Alokasi Dana Strategis</h1>
                <p class="text-gray-500 font-medium max-w-4xl mt-3">Sistem harus memproses data analitik ketersediaan dana mengendap (idle) dan memberikan notifikasi rekomendasi nominal alokasi dana strategis pada dashboard Manajer.</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 min-w-full lg:min-w-[620px]">
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Rekomendasi</p>
                    <p class="text-2xl font-black text-[#013599]">{{ $summary['total_recommendations'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Pending</p>
                    <p class="text-2xl font-black text-amber-600">{{ $summary['pending_count'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Disetujui</p>
                    <p class="text-2xl font-black text-emerald-600">{{ $summary['approved_count'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Ditolak</p>
                    <p class="text-2xl font-black text-red-600">{{ $summary['rejected_count'] }}</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Saldo kas</p>
                <p class="text-xl font-black text-gray-900 mt-2">
                    {{ $latestSnapshot ? 'Rp '.number_format((float) $latestSnapshot->total_cash_balance, 0, ',', '.') : '-' }}
                </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Pinjaman berjalan</p>
                <p class="text-xl font-black text-amber-600 mt-2">
                    {{ $latestSnapshot ? 'Rp '.number_format((float) $latestSnapshot->total_outstanding_loans, 0, ',', '.') : '-' }}
                </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Cadangan operasional</p>
                <p class="text-xl font-black text-emerald-600 mt-2">
                    {{ $latestSnapshot ? 'Rp '.number_format((float) $latestSnapshot->operational_reserve, 0, ',', '.') : '-' }}
                </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <p class="text-[11px] font-bold text-gray-400 uppercase">Dana mengendap</p>
                <p class="text-xl font-black text-[#013599] mt-2">
                    {{ $latestSnapshot ? 'Rp '.number_format((float) $latestSnapshot->idle_fund_amount, 0, ',', '.') : '-' }}
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">Rekomendasi Nominal Alokasi Dana Strategis</h2>
                <p class="text-sm text-gray-500">Sumber data: idle_fund_snapshots, fund_allocations.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Koperasi</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Nominal Rekomendasi</th>
                            <th class="px-6 py-4">Confidence</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($allocations as $allocation)
                            <tr class="hover:bg-blue-50/30">
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $allocation->created_at?->format('d M Y') }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-900">{{ $allocation->koperasi?->nama_koperasi ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $allocation->allocation_category }}</td>
                                <td class="px-6 py-5 text-sm font-black text-[#013599]">Rp {{ number_format((float) $allocation->recommended_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ number_format((float) $allocation->confidence_score, 1) }}%</td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-700">{{ $allocation->status }}</td>
                                <td class="px-6 py-5 text-sm text-gray-500 max-w-md">{{ $allocation->reasoning ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm font-bold text-gray-400">Belum ada rekomendasi alokasi dana strategis dari basis data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
