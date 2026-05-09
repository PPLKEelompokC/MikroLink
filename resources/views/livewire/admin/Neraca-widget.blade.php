<?php

use Livewire\Volt\Component;
use App\Models\Koperasi;
use App\Models\NeracaKeuangan;
use App\Services\NeracaKeuanganService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

new class extends Component {

    public function with(): array
    {
        // Guard: jika tabel belum dimigrasi, tampilkan empty state
        if (! Schema::hasTable('neraca_keuangan')) {
            return ['neraca' => null, 'prev' => null, 'tableMissing' => true];
        }

        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();
        if (! $koperasi) return ['neraca' => null, 'prev' => null, 'tableMissing' => false];

        $neraca = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->orderBy('periode', 'desc')
            ->first();

        $prev = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->orderBy('periode', 'desc')
            ->skip(1)->first();

        return compact('neraca', 'prev') + ['tableMissing' => false];
    }

    public function autoGenerate(NeracaKeuanganService $service): void
    {
        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();
        if ($koperasi) {
            $service->generate($koperasi, Carbon::now()->startOfMonth());
        }
    }
};
?>

<div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6 flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-800">Neraca Keuangan</span>
        </div>
        <a href="{{ route('admin.neraca.index') }}"
           class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full transition-colors">
            Detail →
        </a>
    </div>

    @if(isset($tableMissing) && $tableMissing)
        <div class="flex flex-col items-center gap-3 py-4 text-center">
            <p class="text-xs text-amber-500 font-semibold">⚠️ Jalankan <code class="bg-gray-100 px-1 rounded">php artisan migrate</code> untuk mengaktifkan fitur ini.</p>
        </div>
    @elseif($neraca)
        <div class="flex flex-col gap-2">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $neraca->periode_label }}</p>

            {{-- Aset --}}
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-xs text-gray-500">Total Aset</span>
                </div>
                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neraca->total_aset, 0, ',', '.') }}</span>
            </div>

            {{-- Kewajiban --}}
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                    <span class="text-xs text-gray-500">Kewajiban</span>
                </div>
                <span class="text-sm font-semibold text-red-500">Rp {{ number_format($neraca->total_kewajiban, 0, ',', '.') }}</span>
            </div>

            {{-- Ekuitas --}}
            <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs text-gray-500">Ekuitas</span>
                </div>
                <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($neraca->total_ekuitas, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Status + Likuiditas --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            @php $warna = $neraca->warnaStatus(); @endphp
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-{{ $warna }}-50 text-{{ $warna }}-600 border border-{{ $warna }}-100">
                {{ $neraca->statusKesehatan() }}
            </span>
            <span class="text-[11px] text-gray-400 font-semibold">
                Likuiditas {{ number_format($neraca->rasio_likuiditas, 1) }}%
            </span>
        </div>

        {{-- Perbandingan bulan lalu --}}
        @if($prev)
            @php
                $diffAset = $neraca->total_aset - $prev->total_aset;
                $naik = $diffAset >= 0;
            @endphp
            <div class="flex items-center gap-1.5 text-[11px] {{ $naik ? 'text-emerald-500' : 'text-red-400' }} font-semibold">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="{{ $naik ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                </svg>
                {{ $naik ? '+' : '' }}Rp {{ number_format(abs($diffAset), 0, ',', '.') }} vs bulan lalu
            </div>
        @endif

    @else
        <div class="flex flex-col items-center gap-3 py-4 text-center">
            <p class="text-xs text-gray-400">Belum ada neraca bulan ini</p>
            <button wire:click="autoGenerate"
                    class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors">
                🔄 Generate Otomatis
            </button>
        </div>
    @endif
</div>