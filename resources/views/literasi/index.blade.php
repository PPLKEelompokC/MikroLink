@extends('layouts.app')

@section('title', 'Ruang Tumbuh – Literasi Finansial')

@section('content')
<div class="w-full max-w-[1400px] mx-auto px-10 py-12 flex flex-col gap-10">

    {{-- HERO --}}
    <div class="flex items-center justify-between bg-gradient-to-r from-[#1a2340] to-[#2d3a5c] p-10 rounded-[32px] text-white relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-4 py-1.5 bg-[#e8a838]/20 text-[#e8a838] text-xs font-extrabold uppercase tracking-widest rounded-full mb-4">Ruang Tumbuh</span>
            <h1 class="text-4xl font-bold leading-tight mb-4">Tingkatkan Literasi Finansial Anda 📚</h1>
            <p class="text-white/70 text-[15px] leading-relaxed mb-6">Pelajari konsep keuangan lewat artikel ringkas. Gratis untuk semua anggota MikroLink.</p>
            <div class="flex gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-300">{{ $artikels->total() }}</div>
                    <div class="text-xs text-white/60 mt-1">Artikel Tersedia</div>
                </div>
                <div class="w-px bg-white/20"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#e8a838]">{{ count(\App\Models\LiterasiArtikel::daftarKategori()) }}</div>
                    <div class="text-xs text-white/60 mt-1">Kategori Topik</div>
                </div>
            </div>
        </div>
        <div class="hidden lg:block text-8xl select-none">💡</div>
    </div>

    {{-- FILTER KATEGORI --}}
    <div class="flex flex-wrap gap-3 items-center">
        <span class="text-sm font-bold text-gray-500 uppercase tracking-widest mr-2">Topik:</span>
        <a href="{{ route('literasi.index') }}"
           class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ !$kategoriAktif ? 'bg-[#1a2340] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Semua
        </a>
        @foreach ($daftarKategori as $slug => $info)
            <a href="{{ route('literasi.index', ['kategori' => $slug]) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ $kategoriAktif === $slug ? 'bg-[#1a2340] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $info['icon'] }} {{ $info['label'] }}
            </a>
        @endforeach
    </div>

    {{-- GRID ARTIKEL --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            {{ $kategoriAktif ? ($daftarKategori[$kategoriAktif]['icon'] . ' ' . $daftarKategori[$kategoriAktif]['label']) : '📖 Semua Artikel' }}
        </h2>

        @if ($artikels->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-gray-400">
                <div class="text-6xl mb-4">🔍</div>
                <p class="font-semibold text-lg">Belum ada artikel untuk kategori ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($artikels as $artikel)
                    @php $katInfo = $daftarKategori[$artikel->kategori] ?? ['icon' => '📄', 'label' => $artikel->kategori, 'warna' => 'gray']; @endphp
                    <a href="{{ route('literasi.show', $artikel->slug) }}"
                       class="group bg-white border border-gray-100 rounded-[24px] p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 bg-gray-50">
                                {{ $katInfo['icon'] }}
                            </div>
                            <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest rounded-full bg-gray-100 text-gray-600">
                                {{ $katInfo['label'] }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-[16px] leading-snug group-hover:text-[#e8a838] transition-colors mb-2">
                                {{ $artikel->judul }}
                            </h3>
                            <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">{{ $artikel->ringkasan }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span>⏱ {{ $artikel->estimasi_baca }} menit</span>
                                <span>👁 {{ number_format($artikel->views) }}</span>
                            </div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $artikel->labelLevel() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $artikels->links() }}</div>
        @endif
    </div>

</div>
@endsection