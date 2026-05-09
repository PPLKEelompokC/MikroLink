<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Koperasi;
use App\Models\NeracaKeuangan;
use App\Services\NeracaKeuanganService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

new #[Layout('layouts.app')] class extends Component {

    public string $selectedPeriode = '';
    public bool   $isGenerating    = false;
    public ?string $pesanSukses    = null;

    public function mount(): void
    {
        $this->selectedPeriode = now()->format('Y-m');
    }

    public function with(): array
    {
        if (! Schema::hasTable('neraca_keuangan')) {
            return [
                'koperasi'     => null,
                'riwayat'      => collect(),
                'neracaAktif'  => null,
                'tren'         => collect(),
                'tableMissing' => true,
            ];
        }

        $koperasi = Koperasi::firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 0]
        );

        $riwayat = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->orderBy('periode', 'desc')
            ->take(12)
            ->get();

        $periodeDate = Carbon::createFromFormat('Y-m', $this->selectedPeriode)->endOfMonth();
        $neracaAktif = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->where('periode', $periodeDate->toDateString())
            ->first();

        $tren = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->orderBy('periode', 'asc')
            ->take(6)
            ->get();

        return compact('koperasi', 'riwayat', 'neracaAktif', 'tren') + ['tableMissing' => false];
    }

    public function generateNeraca(NeracaKeuanganService $service): void
    {
        $this->pesanSukses = null;
        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();
        if (! $koperasi) return;

        $periode = Carbon::createFromFormat('Y-m', $this->selectedPeriode)->startOfMonth();
        $service->generate($koperasi, $periode);
        $this->pesanSukses = 'Neraca bulan ' . $periode->translatedFormat('F Y') . ' berhasil digenerate!';
    }

    public function generateSemua(NeracaKeuanganService $service): void
    {
        $this->pesanSukses = null;
        $koperasi = Koperasi::where('id_koperasi', 'KOP-001')->first();
        if ($koperasi) {
            $service->generateLast6Months($koperasi);
            $this->pesanSukses = 'Neraca 6 bulan terakhir berhasil digenerate!';
        }
    }
};
?>

<div class="w-full max-w-[1400px] mx-auto px-6 py-10 flex flex-col gap-8">

{{-- TABLE MISSING STATE --}}
@if(isset($tableMissing) && $tableMissing)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-10 text-center">
        <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-amber-800 mb-2">Tabel Neraca Belum Dimigrasi</h3>
        <p class="text-amber-600 text-sm mb-2">Jalankan perintah berikut di terminal project:</p>
        <code class="block bg-gray-900 text-emerald-400 font-mono text-sm px-6 py-3 rounded-xl mx-auto max-w-md mb-3">php artisan migrate</code>
        <p class="text-amber-500 text-xs">Kemudian jalankan seeder untuk mengisi data awal:</p>
        <code class="block bg-gray-900 text-blue-400 font-mono text-sm px-6 py-3 rounded-xl mx-auto max-w-md">php artisan db:seed --class=NeracaKeuanganSeeder</code>
    </div>
@else

{{-- HEADER --}}
<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 bg-indigo-50 px-3 py-1 rounded-full">Laporan Keuangan</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">Neraca Keuangan Koperasi</h1>
        <p class="text-gray-500 text-sm mt-1">Laporan posisi keuangan otomatis berbasis data real-time</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <input type="month"
               wire:model.live="selectedPeriode"
               max="{{ now()->format('Y-m') }}"
               class="border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white shadow-sm">
        <button wire:click="generateNeraca"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm disabled:opacity-60">
            <svg wire:loading wire:target="generateNeraca" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <svg wire:loading.remove wire:target="generateNeraca" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Generate Bulan Ini
        </button>
        <button wire:click="generateSemua"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm disabled:opacity-60">
            <span wire:loading.remove wire:target="generateSemua">Sync 6 Bulan</span>
            <span wire:loading wire:target="generateSemua">Memproses...</span>
        </button>
    </div>
</div>

{{-- PESAN SUKSES --}}
@if($pesanSukses)
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold text-sm px-5 py-3 rounded-xl">
    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ $pesanSukses }}
</div>
@endif

@if($neracaAktif)

{{-- KARTU RINGKASAN --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-lg shadow-blue-100">
        <div class="flex items-center justify-between mb-4">
            <span class="text-blue-200 text-xs font-bold uppercase tracking-widest">Total Aset</span>
            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold mb-1">Rp {{ number_format($neracaAktif->total_aset, 0, ',', '.') }}</div>
        <div class="text-blue-200 text-xs">Kas + Piutang Pinjaman</div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Kewajiban</span>
            <div class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">Rp {{ number_format($neracaAktif->total_kewajiban, 0, ',', '.') }}</div>
        <div class="text-gray-400 text-xs">Penarikan Pending</div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-neutral-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Ekuitas</span>
            <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-emerald-600 mb-1">Rp {{ number_format($neracaAktif->total_ekuitas, 0, ',', '.') }}</div>
        @php $warna = $neracaAktif->warnaStatus(); @endphp
        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $warna }}-50 text-{{ $warna }}-600 border border-{{ $warna }}-100">
            {{ $neracaAktif->statusKesehatan() }}
        </span>
    </div>

</div>

{{-- T-ACCOUNT NERACA --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ASET --}}
    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
            <div class="flex items-center gap-2">
                <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                <h3 class="font-bold text-gray-800 text-base">ASET</h3>
                <span class="ml-auto text-xs text-gray-400 font-semibold">{{ $neracaAktif->periode_label }}</span>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Kas & Bank</p>
                    <p class="text-xs text-gray-400">Saldo kas koperasi</p>
                </div>
                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neracaAktif->kas, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Piutang Pinjaman</p>
                    <p class="text-xs text-gray-400">Pinjaman aktif anggota</p>
                </div>
                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neracaAktif->piutang_pinjaman, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 bg-blue-50">
                <span class="text-sm font-bold text-blue-700">TOTAL ASET</span>
                <span class="text-base font-bold text-blue-700">Rp {{ number_format($neracaAktif->total_aset, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-dashed border-gray-200">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan: Simpanan Anggota</p>
            <div class="flex justify-between text-xs py-1.5">
                <span class="text-gray-500">Simpanan Pokok</span>
                <span class="font-semibold text-gray-700">Rp {{ number_format($neracaAktif->simpanan_pokok, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs py-1.5">
                <span class="text-gray-500">Simpanan Wajib</span>
                <span class="font-semibold text-gray-700">Rp {{ number_format($neracaAktif->simpanan_wajib, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs py-1.5">
                <span class="text-gray-500">Simpanan Sukarela</span>
                <span class="font-semibold text-gray-700">Rp {{ number_format($neracaAktif->simpanan_sukarela, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- KEWAJIBAN + EKUITAS --}}
    <div class="flex flex-col gap-4">

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-4 border-b border-red-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-6 bg-red-400 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-base">KEWAJIBAN</h3>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Kewajiban Penarikan</p>
                        <p class="text-xs text-gray-400">Withdrawal pending anggota</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neracaAktif->kewajiban_tarik_pending, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-4 bg-red-50">
                    <span class="text-sm font-bold text-red-600">TOTAL KEWAJIBAN</span>
                    <span class="text-base font-bold text-red-600">Rp {{ number_format($neracaAktif->total_kewajiban, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-emerald-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                    <h3 class="font-bold text-gray-800 text-base">EKUITAS / MODAL</h3>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Modal Disetor</p>
                        <p class="text-xs text-gray-400">Simpanan pokok + wajib</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neracaAktif->modal_disetor, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Sisa Hasil Usaha (SHU)</p>
                        <p class="text-xs text-gray-400">Aset bersih setelah kewajiban & modal</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($neracaAktif->sisa_hasil_usaha, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-4 bg-emerald-50">
                    <span class="text-sm font-bold text-emerald-700">TOTAL EKUITAS</span>
                    <span class="text-base font-bold text-emerald-700">Rp {{ number_format($neracaAktif->total_ekuitas, 0, ',', '.') }}</span>
                </div>
            </div>
            @php $selisih = abs($neracaAktif->total_aset - ($neracaAktif->total_kewajiban + $neracaAktif->total_ekuitas)); @endphp
            <div class="px-6 py-3 border-t border-dashed border-gray-200 flex items-center gap-2">
                @if($selisih < 1)
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs text-emerald-600 font-bold">Neraca Seimbang</span>
                @else
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-xs text-amber-600 font-bold">Selisih: Rp {{ number_format($selisih, 0, ',', '.') }}</span>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- RASIO KESEHATAN --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-gray-700">Rasio Likuiditas</span>
            @if($neracaAktif->rasio_likuiditas >= 100)
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Baik</span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">Perhatikan</span>
            @endif
        </div>
        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($neracaAktif->rasio_likuiditas, 1) }}%</div>
        <div class="text-xs text-gray-400 mb-4">Kas / Total Kewajiban x 100</div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-700 {{ $neracaAktif->rasio_likuiditas >= 100 ? 'bg-emerald-500' : 'bg-amber-400' }}"
                 style="width: {{ min(100, $neracaAktif->rasio_likuiditas / 2) }}%"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-gray-700">Rasio Kecukupan Modal</span>
            @if($neracaAktif->rasio_kecukupan >= 60)
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Baik</span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">Perhatikan</span>
            @endif
        </div>
        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($neracaAktif->rasio_kecukupan, 1) }}%</div>
        <div class="text-xs text-gray-400 mb-4">Total Ekuitas / Total Aset x 100</div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-700 {{ $neracaAktif->rasio_kecukupan >= 60 ? 'bg-emerald-500' : 'bg-amber-400' }}"
                 style="width: {{ min(100, $neracaAktif->rasio_kecukupan) }}%"></div>
        </div>
    </div>

</div>

@else
{{-- EMPTY STATE --}}
<div class="bg-white rounded-2xl border border-dashed border-gray-200 p-16 text-center">
    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <h3 class="text-lg font-bold text-gray-700 mb-2">Belum Ada Neraca untuk Periode Ini</h3>
    <p class="text-gray-400 text-sm mb-6">Klik <strong>Generate Bulan Ini</strong> untuk membuat laporan otomatis.</p>
    <button wire:click="generateNeraca"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-6 py-3 rounded-xl transition-all shadow-sm">
        Generate Sekarang
    </button>
</div>
@endif

{{-- TREN CHART --}}
@if($tren->isNotEmpty())
<div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-8">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">Tren Neraca Keuangan</h2>
        <p class="text-sm text-gray-400 mt-0.5">Perbandingan Aset vs Kewajiban vs Ekuitas (6 bulan terakhir)</p>
    </div>
    <div id="neracaChart" style="min-height: 280px;"></div>
</div>
@endif

{{-- RIWAYAT LAPORAN --}}
@if($riwayat->isNotEmpty())
<div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Riwayat Laporan Neraca</h3>
        <span class="text-xs text-gray-400 font-semibold">{{ $riwayat->count() }} periode tersedia</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Periode</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Total Aset</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Kewajiban</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Ekuitas</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Likuiditas</th>
                    <th class="text-center px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($riwayat as $n)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                    wire:click="$set('selectedPeriode', '{{ $n->periode->format('Y-m') }}')">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $n->periode_label }}</td>
                    <td class="px-6 py-4 text-right font-bold text-blue-600">Rp {{ number_format($n->total_aset, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-red-500 font-medium">Rp {{ number_format($n->total_kewajiban, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-emerald-600 font-bold">Rp {{ number_format($n->total_ekuitas, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-gray-600 font-medium">{{ number_format($n->rasio_likuiditas, 1) }}%</td>
                    <td class="px-6 py-4 text-center">
                        @php $wn = $n->warnaStatus(); @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $wn }}-50 text-{{ $wn }}-600 border border-{{ $wn }}-100">
                            {{ $n->statusKesehatan() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif
{{-- END tableMissing @else --}}

</div>

@push('scripts')
@php
$trenJson = [];
foreach ($tren as $n) {
    $trenJson[] = [
        'label'      => $n->periode_label,
        'total_aset' => (float) $n->total_aset,
        'kewajiban'  => (float) $n->total_kewajiban,
        'ekuitas'    => (float) $n->total_ekuitas,
    ];
}
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trenData = {!! json_encode($trenJson) !!};
    const chartEl = document.getElementById('neracaChart');
    if (!chartEl || !trenData.length) return;

    const opts = {
        series: [
            { name: 'Total Aset', data: trenData.map(function(d) { return d.total_aset; }) },
            { name: 'Kewajiban',  data: trenData.map(function(d) { return d.kewajiban; }) },
            { name: 'Ekuitas',    data: trenData.map(function(d) { return d.ekuitas; }) },
        ],
        chart: {
            type: 'bar', height: 280,
            fontFamily: "'Plus Jakarta Sans', sans-serif",
            toolbar: { show: false },
        },
        colors: ['#3b82f6', '#f87171', '#10b981'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        xaxis: {
            categories: trenData.map(function(d) { return d.label; }),
            labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
        },
        yaxis: {
            labels: {
                style: { colors: '#9ca3af', fontSize: '11px' },
                formatter: function(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); }
            }
        },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { position: 'top', fontWeight: 600 },
        tooltip: {
            y: { formatter: function(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); } }
        },
        dataLabels: { enabled: false },
    };

    const chart = new ApexCharts(chartEl, opts);
    chart.render();
});
</script>
@endpush