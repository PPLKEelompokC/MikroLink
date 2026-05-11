@extends('layouts.app')

@section('title', 'Workflow Persetujuan Berjenjang - MikroLink')

@section('content')
    <main class="w-full max-w-[1400px] mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        @include('system-requirements.partials.navigation')

        <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Workflow Persetujuan Berjenjang</h1>
                <p class="text-gray-500 font-medium max-w-4xl mt-3">Sistem harus merutekan status pengajuan pinjaman secara berurutan dan mengunci fungsi pencairan hingga mendapatkan persetujuan dari akun Admin dan Manajer.</p>
            </div>
            <div class="grid grid-cols-3 gap-3 min-w-full lg:min-w-[460px]">
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Pengajuan</p>
                    <p class="text-2xl font-black text-[#013599]">{{ $summary['total_requests'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Pencairan terkunci</p>
                    <p class="text-2xl font-black text-red-600">{{ $summary['locked_count'] }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Siap pencairan</p>
                    <p class="text-2xl font-black text-emerald-600">{{ $summary['ready_count'] }}</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            @foreach($workflow as $item)
                <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <p class="text-[11px] font-bold text-[#013599] uppercase">Urutan</p>
                    <h2 class="text-base font-extrabold text-gray-900 mt-2">{{ $item['step'] }}</h2>
                    <p class="text-sm text-gray-500 mt-3 leading-relaxed">{{ $item['rule'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">Status Pengajuan Pinjaman</h2>
                <p class="text-sm text-gray-500">Sumber data: loans.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">ID Pinjaman</th>
                            <th class="px-6 py-4">Anggota</th>
                            <th class="px-6 py-4">Pengajuan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tahap</th>
                            <th class="px-6 py-4">Admin</th>
                            <th class="px-6 py-4">Manajer</th>
                            <th class="px-6 py-4">Pencairan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($requests as $request)
                            <tr class="hover:bg-blue-50/30">
                                <td class="px-6 py-5 text-sm font-black text-[#013599]">{{ $request['loan_id_number'] }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-900">{{ $request['member'] }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    {{ $request['type'] }}
                                    <span class="block text-xs text-gray-400">Rp {{ number_format((float) $request['amount'], 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-700">{{ $request['status'] }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $request['stage'] }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $request['admin_reviewer'] ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm text-gray-600">{{ $request['manager_reviewer'] ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm font-bold {{ $request['is_disbursement_locked'] ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $request['is_disbursement_locked'] ? 'Terkunci' : 'Terbuka' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm font-bold text-gray-400">Belum ada data pengajuan pinjaman dari basis data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
