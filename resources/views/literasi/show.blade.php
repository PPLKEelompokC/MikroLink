@extends('layouts.app')

@section('title', $artikel->judul . ' | Ruang Tumbuh')

@section('content')
@php $katInfo = \App\Models\LiterasiArtikel::daftarKategori()[$artikel->kategori] ?? ['icon' => '📄', 'label' => $artikel->kategori, 'warna' => 'gray']; @endphp

<div class="w-full max-w-[900px] mx-auto px-6 py-12">

    {{-- BREADCRUMB --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="{{ route('literasi.index') }}" class="hover:text-[#e8a838] transition-colors">Ruang Tumbuh</a>
        <span>/</span>
        <a href="{{ route('literasi.index', ['kategori' => $artikel->kategori]) }}" class="hover:text-[#e8a838] transition-colors">{{ $katInfo['label'] }}</a>
        <span>/</span>
        <span class="text-gray-600 truncate">{{ Str::limit($artikel->judul, 40) }}</span>
    </div>

    {{-- HEADER --}}
    <div class="mb-10">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <span class="text-3xl">{{ $katInfo['icon'] }}</span>
            <span class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-extrabold uppercase tracking-widest rounded-full">
                {{ $katInfo['label'] }}
            </span>
            <span class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">
                {{ $artikel->labelLevel() }}
            </span>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 leading-tight mb-5">{{ $artikel->judul }}</h1>
        <p class="text-lg text-gray-500 leading-relaxed mb-6">{{ $artikel->ringkasan }}</p>
        <div class="flex items-center gap-6 text-sm text-gray-400 pb-8 border-b border-gray-100">
            <span>⏱ {{ $artikel->estimasi_baca }} menit baca</span>
            <span>👁 {{ number_format($artikel->views) }} kali dilihat</span>
            <span>📅 {{ $artikel->created_at->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- KONTEN --}}
    <div class="prose prose-lg max-w-none
                prose-headings:font-bold prose-headings:text-gray-900
                prose-p:text-gray-600 prose-p:leading-relaxed
                prose-li:text-gray-600 prose-strong:text-gray-800
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-ul:space-y-2 prose-ol:space-y-2 mb-16">
        {!! $artikel->konten !!}
    </div>

    {{-- ARTIKEL TERKAIT --}}
    @if ($artikelTerkait->isNotEmpty())
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($artikelTerkait as $item)
            <a href="{{ route('literasi.show', $item->slug) }}"
               class="group bg-white border border-gray-100 rounded-[20px] p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <p class="text-xs text-gray-400 mb-2">{{ $katInfo['icon'] }} {{ $katInfo['label'] }}</p>
                <h3 class="font-bold text-gray-800 text-sm leading-snug group-hover:text-[#e8a838] transition-colors">{{ $item->judul }}</h3>
                <p class="text-xs text-gray-400 mt-3">⏱ {{ $item->estimasi_baca }} menit</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- TOMBOL KEMBALI --}}
    <div class="mt-10">
        <a href="{{ route('literasi.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-[#1a2340] text-white font-bold text-sm rounded-2xl hover:bg-[#2d3a5c] transition-all">
            ← Kembali ke Ruang Tumbuh
        </a>
    </div>

</div>
@endsection