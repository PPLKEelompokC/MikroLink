@extends('layouts.app')

@section('title', 'Indeks Kepercayaan & Kelayakan - MikroLink')

@section('content')
    <main class="w-full max-w-[1400px] mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        @include('system-requirements.partials.navigation')

        <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Indeks Kepercayaan & Kelayakan</h1>
                <p class="text-gray-500 font-medium max-w-4xl mt-3">Sistem harus dapat menghitung dan menampilkan skor kelayakan pengajuan pinjaman menggunakan parameter riwayat partisipasi dan status kepatuhan pembayaran dari basis data.</p>
            </div>
            <div class="grid grid-cols-3 gap-3 min-w-full lg:min-w-[460px]">
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Data anggota</p>
                    <p class="text-2xl font-black text-[#013599]">{{ $summary['total_members'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Rata-rata skor</p>
                    <p class="text-2xl font-black text-emerald-600">{{ $summary['average_score'] ?? '-' }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Pengajuan terkait</p>
                    <p class="text-2xl font-black text-amber-600">{{ $summary['with_active_submission'] }}</p>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">Skor Kelayakan Pengajuan Pinjaman</h2>
                <p class="text-sm text-gray-500">Sumber data: {{ $summary['data_source'] }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Anggota</th>
                            <th class="px-6 py-4">Pengajuan Pinjaman</th>
                            <th class="px-6 py-4">Riwayat Partisipasi</th>
                            <th class="px-6 py-4">Status Kepatuhan Pembayaran</th>
                            <th class="px-6 py-4">Skor Kelayakan</th>
                            <th class="px-6 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($members as $member)
                            <tr class="hover:bg-blue-50/30">
                                <td class="px-6 py-5 text-sm font-bold text-gray-900">{{ $member['name'] }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    @if($member['loan_id_number'])
                                        <span class="font-bold text-[#013599]">{{ $member['loan_id_number'] }}</span>
                                        <span class="block text-xs text-gray-400">{{ $member['loan_type'] }} - {{ $member['loan_status'] }}</span>
                                    @else
                                        <span class="text-gray-400">Belum ada pengajuan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $member['participation_score'] }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    Integritas {{ $member['integrity_score'] }} / Konsistensi {{ $member['reliability_score'] }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#013599]" style="width: {{ min(100, max(0, (float) $member['final_index'])) }}%"></div>
                                        </div>
                                        <span class="text-sm font-black text-gray-900">{{ number_format((float) $member['final_index'], 1) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-500">{{ $member['notes'] ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm font-bold text-gray-400">Belum ada data skor kelayakan dari basis data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
