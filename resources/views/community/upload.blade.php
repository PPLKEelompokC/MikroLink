@extends('layouts.app')

@section('title', 'Upload Dokumen Legalitas - MikroLink')

@section('content')
    <main class="w-full max-w-5xl mx-auto px-6 lg:px-10 py-10 flex flex-col gap-8">
        <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#e8a838]">PBI-04</p>
                <h1 class="text-[32px] lg:text-[40px] font-extrabold text-gray-950 leading-tight">Validasi Dokumen Komunitas</h1>
                <p class="text-gray-500 font-medium max-w-3xl mt-3">Unggah berkas legalitas NIB atau Surat Keterangan agar admin koperasi dapat memvalidasi kelayakan administrasi dukungan modal.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-lg border border-gray-200 bg-white text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                Kembali ke Dashboard
            </a>
        </section>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-6 items-start">
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-extrabold text-gray-900">Kirim Dokumen Legalitas</h2>
                    <p class="text-sm text-gray-500 mt-1">Format yang diterima: PDF, JPG, JPEG, atau PNG. Maksimal 2 MB.</p>
                </div>

                <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data" class="p-6 flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="document_name" class="text-sm font-bold text-gray-700">Jenis Dokumen</label>
                        <select id="document_name" name="document_name" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100">
                            <option value="NIB" @selected(old('document_name') === 'NIB')>NIB (Nomor Induk Berusaha)</option>
                            <option value="Surat Keterangan" @selected(old('document_name') === 'Surat Keterangan')>Surat Keterangan</option>
                        </select>
                        @error('document_name')
                            <p class="text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="file" class="text-sm font-bold text-gray-700">Berkas Dokumen</label>
                        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="w-full rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-sm font-semibold text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#013599] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:bg-gray-100">
                        @error('file')
                            <p class="text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="note" class="text-sm font-bold text-gray-700">Catatan Tambahan</label>
                        <textarea id="note" name="note" rows="4" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800 outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100" placeholder="Contoh: Dokumen usaha warung sembako RT 03">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#013599] px-6 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-blue-800 transition-colors">
                        Kirim untuk Validasi
                    </button>
                </form>
            </div>

            <aside class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-extrabold text-gray-900">Riwayat Pengajuan</h2>
                    <p class="text-sm text-gray-500 mt-1">Pantau status validasi dokumen yang sudah dikirim.</p>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($documents as $document)
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'rejected' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                        @endphp
                        <div class="p-5 flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-extrabold text-gray-900">{{ $document->document_name }}</p>
                                    <p class="text-xs font-semibold text-gray-400">{{ $document->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <span class="rounded-full border px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest {{ $statusClasses[$document->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $document->status }}
                                </span>
                            </div>

                            @if($document->note)
                                <p class="rounded-lg bg-gray-50 px-4 py-3 text-xs font-medium leading-relaxed text-gray-500">{{ $document->note }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-sm font-bold text-gray-400">Belum ada dokumen yang dikirim.</p>
                        </div>
                    @endforelse
                </div>
            </aside>
        </section>
    </main>
@endsection
