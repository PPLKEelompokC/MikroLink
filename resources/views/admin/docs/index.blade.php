@extends('layouts.app')

@section('title', 'Validasi KYC - Admin MikroLink')

@section('content')
<div class="w-full max-w-[1400px] mx-auto px-10 py-12">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tighter">Validasi KYC Digital</h1>
            <p class="text-gray-500 mt-1">Review dan verifikasi identitas anggota koperasi.</p>
        </div>
        <div class="flex gap-2">
            <span class="px-4 py-2 bg-amber-50 text-amber-700 rounded-2xl text-xs font-bold border border-amber-100">
                Total Pengajuan: {{ $documents->count() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($documents as $doc)
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                {{-- KTP Preview --}}
                <div class="relative h-48 bg-gray-100 group cursor-pointer">
                    {{-- Try to show the real uploaded file via secure route, fallback to sample image --}}
                    @php
                        $fileUrl = route('admin.docs.view', $doc->id);
                        $isKtp = Str::contains(strtolower($doc->document_name), 'ktp') || Str::contains(strtolower($doc->file_path), 'ktp');
                    @endphp

                    <a href="{{ $fileUrl }}" target="_blank">
                        <img src="{{ $fileUrl }}" 
                             onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/b/bb/KTP_Indonesia.png'"
                             class="w-full h-full object-cover transition-transform group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <span class="bg-white/90 px-4 py-2 rounded-full text-[10px] font-bold text-gray-900 shadow-xl">Klik untuk Lihat Full</span>
                        </div>
                    </a>
                    
                    <div class="absolute top-4 right-4">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-500 text-white',
                                'approved' => 'bg-emerald-500 text-white',
                                'rejected' => 'bg-red-500 text-white',
                            ];
                            $currentClass = $statusClasses[$doc->status] ?? 'bg-gray-500 text-white';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-widest shadow-lg {{ $currentClass }}">
                            {{ $doc->status }}
                        </span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Anggota</h4>
                        <p class="text-lg font-extrabold text-gray-900 leading-tight">{{ $doc->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $doc->user->email }}</p>
                    </div>

                    <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jenis Dokumen</span>
                            <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-widest">{{ $doc->document_name }}</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-tight italic">"{{ $doc->note }}"</p>
                    </div>

                    @if($doc->status === 'pending')
                        <div class="mt-auto space-y-3">
                            <form action="{{ route('admin.docs.update', $doc->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <input type="hidden" name="note" value="Dokumen KTP telah diverifikasi.">
                                <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-2xl transition-all shadow-lg shadow-emerald-100">
                                    Approve Verifikasi
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.docs.update', $doc->id) }}" method="POST" x-data="{ showReject: false }">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                
                                <div x-show="showReject" class="mb-3">
                                    <textarea name="note" class="w-full p-3 text-xs border border-gray-200 rounded-xl focus:ring-red-500" placeholder="Alasan penolakan..." required></textarea>
                                </div>
                                
                                <button type="button" @click="showReject = !showReject" x-show="!showReject" class="w-full py-3 bg-white border border-red-200 text-red-500 hover:bg-red-50 text-xs font-bold rounded-2xl transition-all">
                                    Tolak (Upload Ulang)
                                </button>
                                
                                <button type="submit" x-show="showReject" class="w-full py-3 bg-red-500 text-white text-xs font-bold rounded-2xl transition-all">
                                    Konfirmasi Penolakan
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-auto pt-4 border-t border-gray-50 text-center">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Diproses pada {{ $doc->updated_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white rounded-[40px] border border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Tidak Ada Antrian KYC</h3>
                <p class="text-gray-500 text-sm">Semua dokumen identitas anggota sudah diproses.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
