@extends('layouts.app')

@section('title', 'Verifikasi & Keanggotaan - Admin MikroLink')

@section('content')
<main
    class="w-full max-w-[1400px] mx-auto px-6 lg:px-10 py-12"
    x-data="{ selectedId: {{ $documents->first()?->id ?? 'null' }}, rejectId: null }"
>
    <section class="mb-10">
        <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Verifikasi & Keanggotaan</h1>
        <p class="text-gray-600 font-semibold max-w-6xl mt-3 leading-relaxed">
            Sistem verifikasi terpadu yang mencakup pemeriksaan dokumen legalitas, status KYC, dan persetujuan admin untuk memastikan anggota memenuhi standar administrasi koperasi.
        </p>
    </section>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-extrabold text-gray-800">Pending KYC</p>
                <p class="text-4xl font-black text-gray-950 mt-6">{{ $stats['pending'] }}</p>
            </div>
            <svg class="w-14 h-14 text-gray-950" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5Zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5Z"/></svg>
        </div>

        <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-extrabold text-gray-800">Terverifikasi</p>
                <p class="text-4xl font-black text-gray-950 mt-6">{{ $stats['verified'] }}</p>
            </div>
            <svg class="w-14 h-14 text-[#f5a400]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 12l2 2 4-4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.36-6.36-2.12 2.12M7.76 16.24l-2.12 2.12m0-12.72 2.12 2.12m8.48 8.48 2.12 2.12"/><circle cx="12" cy="12" r="5" stroke-width="2.4"/></svg>
        </div>

        <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-extrabold text-gray-800">Menunggu Approval</p>
                <p class="text-4xl font-black text-gray-950 mt-6">{{ $stats['waiting_approval'] }}</p>
            </div>
            <svg class="w-14 h-14 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 7v6l4 2"/></svg>
        </div>

        <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6">
            <p class="text-sm font-extrabold text-gray-800">Disetujui Hari Ini</p>
            <p class="text-4xl font-black text-gray-950 mt-6">{{ $stats['approved_today'] }}</p>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-[430px_1fr] gap-6 items-start">
        <aside class="bg-white border border-gray-100 rounded-lg shadow-sm p-5">
            <div class="rounded-lg bg-gray-50 border border-gray-100 px-5 py-4 flex items-center gap-3 mb-6">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <span class="text-sm font-extrabold text-gray-500">Cari berdasarkan nama atau ID...</span>
            </div>

            <div class="flex flex-col gap-4">
                @forelse($documents as $doc)
                    @php
                        $statusLabel = [
                            'pending' => 'KYC PENDING',
                            'approved' => 'TERVERIFIKASI',
                            'rejected' => 'DITOLAK',
                        ][$doc->status] ?? strtoupper($doc->status);

                        $statusClasses = [
                            'pending' => 'bg-blue-100 text-blue-700',
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                        ][$doc->status] ?? 'bg-gray-100 text-gray-600';

                        $progress = [
                            'pending' => 25,
                            'rejected' => 10,
                            'approved' => 100,
                        ][$doc->status] ?? 25;
                    @endphp

                    <button
                        type="button"
                        @click="selectedId = {{ $doc->id }}; rejectId = null"
                        class="w-full text-left rounded-lg border p-4 transition-colors"
                        :class="selectedId === {{ $doc->id }} ? 'border-[#f5a400] bg-amber-50/20' : 'border-gray-100 bg-white hover:border-amber-200'"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21c1.5-4 14.5-4 16 0"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-lg font-extrabold text-gray-950 truncate">{{ $doc->user?->name ?? 'Anggota Koperasi' }}</p>
                                    <p class="text-xs font-semibold text-gray-700">VER-{{ $doc->created_at->format('Y') }}-{{ str_pad((string) $doc->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    <span class="inline-flex mt-2 rounded-full px-3 py-1 text-[10px] font-extrabold {{ $statusClasses }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-1 mt-1 shrink-0">
                                <span class="w-4 h-4 rounded-full bg-[#f5a400]"></span>
                                <span class="w-4 h-4 rounded-full bg-[#194dbc]"></span>
                                <span class="w-4 h-4 rounded-full bg-emerald-500"></span>
                                <span class="w-4 h-4 rounded-full bg-transparent"></span>
                            </div>
                        </div>
                        <div class="mt-5 flex items-center gap-3">
                            <div class="flex-1 h-2 rounded-full bg-gray-200 overflow-hidden">
                                <div class="h-full rounded-full bg-[#f5a400]" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700">{{ $progress }}%</span>
                        </div>
                    </button>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-200 py-12 text-center">
                        <p class="text-sm font-bold text-gray-400">Tidak ada dokumen yang menunggu verifikasi.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 min-h-[620px]">
            @forelse($documents as $doc)
                @php
                    $fileUrl = route('admin.docs.view', $doc->id);
                    $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
                    $isApproved = $doc->status === 'approved';
                    $isRejected = $doc->status === 'rejected';
                @endphp

                <div x-show="selectedId === {{ $doc->id }}" x-cloak>
                    <div class="border-b border-gray-200 pb-5 mb-6">
                        <h2 class="text-xl font-extrabold text-gray-950">{{ $doc->user?->name ?? 'Anggota Koperasi' }}</h2>
                        <p class="text-sm font-semibold text-gray-700 mt-1">VER-{{ $doc->created_at->format('Y') }}-{{ str_pad((string) $doc->id, 3, '0', STR_PAD_LEFT) }} | {{ $doc->created_at->format('d M Y') }}</p>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-extrabold text-gray-950 mb-5">Status Verifikasi</h3>
                        <div class="grid grid-cols-4 items-start text-center">
                            @foreach([
                                ['label' => 'KYC', 'done' => true],
                                ['label' => 'Validasi', 'done' => $doc->status !== 'pending'],
                                ['label' => 'Scoring', 'done' => $isApproved],
                                ['label' => 'Approval', 'done' => $isApproved],
                            ] as $index => $step)
                                <div class="relative flex flex-col items-center gap-2">
                                    @if($index > 0)
                                        <div class="absolute top-6 right-1/2 w-full h-0.5 {{ $step['done'] ? 'bg-[#f5a400]' : 'bg-gray-300' }}"></div>
                                    @endif
                                    <div class="relative z-10 w-12 h-12 rounded-full border-2 flex items-center justify-center text-lg font-black {{ $step['done'] ? 'border-[#f5a400] bg-[#f5a400] text-white' : 'border-[#f5a400] bg-white text-gray-950' }}">
                                        @if($step['done'])
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <p class="text-sm font-extrabold text-gray-950">{{ $step['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 shadow-sm mb-5">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <svg class="w-7 h-7 text-gray-950" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5Zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5Z"/></svg>
                                    <h3 class="text-lg font-extrabold text-gray-950">Data Anggota</h3>
                                </div>
                                <p class="text-xs font-bold text-gray-400">Email</p>
                                <p class="text-sm font-bold text-gray-900">{{ $doc->user?->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400">Tipe Aplikasi</p>
                                <p class="text-sm font-bold text-gray-900">Anggota Baru</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 shadow-sm mb-5">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <svg class="w-7 h-7 text-gray-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 4 7v5c0 5 3.4 8.7 8 9 4.6-.3 8-4 8-9V7l-8-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4"/></svg>
                                    <h3 class="text-lg font-extrabold text-gray-950">Validasi {{ $doc->document_name }}</h3>
                                </div>
                                <p class="text-xs font-bold text-gray-400">Nomor Dokumen</p>
                                <p class="text-sm font-bold text-gray-900">{{ strtoupper($doc->document_name) }}-****{{ str_pad((string) $doc->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400">Status Validasi</p>
                                <p class="text-sm font-bold {{ $isApproved ? 'text-emerald-600' : ($isRejected ? 'text-red-600' : 'text-amber-600') }}">{{ ucfirst($doc->status) }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-emerald-100 px-4 py-2 text-xs font-extrabold text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                LIHAT FILE {{ strtoupper($extension ?: 'DOC') }}
                            </a>
                            <span class="inline-flex items-center gap-2 rounded-lg bg-emerald-100 px-4 py-2 text-xs font-extrabold text-emerald-700">{{ $doc->document_name }}</span>
                            @if($doc->note)
                                <span class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-xs font-bold text-gray-600">{{ $doc->note }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-emerald-100 p-5 mb-5">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 3.5 8 8l-4.5 1.5L8 11l1.5 4.5L11 11l4.5-1.5L11 8 9.5 3.5Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v4m-2-2h4"/></svg>
                            <h3 class="text-sm font-extrabold text-gray-950">Administrasi Dokumen</h3>
                        </div>
                        <div class="flex flex-col items-center justify-center py-4 text-center">
                            <svg class="w-12 h-12 text-emerald-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 7v6l4 2"/></svg>
                            <p class="text-sm font-bold text-gray-800">{{ $doc->status === 'pending' ? 'Menunggu keputusan validasi admin.' : 'Dokumen telah diproses oleh admin.' }}</p>
                        </div>
                    </div>

                    @if($doc->status === 'pending')
                        <div class="flex flex-col gap-4">
                            <div x-show="rejectId === {{ $doc->id }}" x-cloak>
                                <form action="{{ route('admin.docs.update', $doc->id) }}" method="POST" class="rounded-lg border border-red-100 bg-red-50 p-4">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <label class="text-xs font-extrabold text-red-700 uppercase">Alasan Penolakan</label>
                                    <textarea name="note" rows="3" class="mt-2 w-full rounded-lg border border-red-200 bg-white p-3 text-sm outline-none focus:border-red-400" required></textarea>
                                    <button type="submit" class="mt-3 w-full rounded-lg bg-red-500 px-5 py-3 text-sm font-extrabold text-white hover:bg-red-600 transition-colors">Konfirmasi Penolakan</button>
                                </form>
                            </div>

                            <div class="flex justify-end gap-4">
                                <button type="button" @click="rejectId = rejectId === {{ $doc->id }} ? null : {{ $doc->id }}" class="w-40 rounded-lg bg-gray-500 px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-600 transition-colors">
                                    Tolak
                                </button>
                                <form action="{{ route('admin.docs.update', $doc->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <input type="hidden" name="note" value="Dokumen legalitas telah diverifikasi.">
                                    <button type="submit" class="w-56 rounded-lg bg-[#f5a400] px-5 py-3 text-sm font-extrabold text-white hover:bg-amber-500 transition-colors">
                                        Setujui & Lanjutkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 text-center">
                            <p class="text-sm font-bold text-gray-500">Diproses pada {{ $doc->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="min-h-[480px] flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tidak Ada Antrian Verifikasi</h3>
                    <p class="text-gray-500 text-sm">Semua dokumen legalitas anggota sudah diproses.</p>
                </div>
            @endforelse
        </section>
    </section>
</main>
@endsection
