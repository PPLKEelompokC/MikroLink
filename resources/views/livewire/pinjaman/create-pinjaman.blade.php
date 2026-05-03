<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Loan;

new #[Layout('layouts.app')] class extends Component {

    public string $type = 'Pinjaman Usaha';
    public string $amount = '';
    public string $duration = '12';
    public string $reason = '';

    public function save()
    {
        $this->validate([
            'type'     => 'required|in:Pinjaman Usaha,Pinjaman Konsumsi,Pinjaman Darurat',
            'amount'   => 'required|numeric|min:100000',
            'duration' => 'required|integer|min:1|max:60',
            'reason'   => 'required|min:20',
        ], [
            'amount.min'   => 'Minimal pengajuan adalah Rp 100.000.',
            'reason.min'   => 'Alasan pengajuan minimal 20 karakter.',
            'duration.max' => 'Maksimal tenor adalah 60 bulan.',
        ]);

        Loan::create([
            'user_id'  => auth()->id(),
            'type'    => $this->type,
            'amount'   => $this->amount,
            'duration' => $this->duration,
            'reason'   => $this->reason,
            'status'   => 'Baru',
            'progress_percentage' => 0,
        ]);

        $this->dispatch('pinjaman-berhasil');
    }
};
?>

<div>
    {{-- Toast Notifikasi --}}
    <div
        x-data="{ show: false }"
        x-on:pinjaman-berhasil.window="show = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 2500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-emerald-200 shadow-xl rounded-2xl px-5 py-4 max-w-sm"
        style="display: none;">
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-gray-900 text-sm">Pengajuan Berhasil!</p>
            <p class="text-xs text-gray-500 mt-0.5">Mengalihkan ke dashboard...</p>
        </div>
    </div>

    <div class="w-full max-w-2xl mx-auto py-10 px-6">

        {{-- Tombol Back --}}
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#e8a838] rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Formulir Pengajuan Pinjaman</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Isi detail pengajuan pinjaman Anda.</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form wire:submit="save" class="p-8 space-y-6">

                {{-- Jenis Pinjaman --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Jenis Pinjaman</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['Pinjaman Usaha' => 'Usaha', 'Pinjaman Konsumsi' => 'Konsumsi', 'Pinjaman Darurat' => 'Darurat'] as $value => $label)
                            <button
                                type="button"
                                wire:click="$set('type', '{{ $value }}')"
                                class="flex flex-col items-center justify-center p-4 border-2 rounded-xl transition-all
                                    {{ $type === $value
                                        ? 'border-[#e8a838] bg-amber-50 text-amber-700'
                                        : 'border-gray-200 text-gray-500 hover:border-amber-300 hover:bg-amber-50/50' }}">
                                <span class="font-bold text-sm">{{ $label }}</span>
                                <span class="text-[10px] font-normal mt-0.5 {{ $type === $value ? 'text-amber-500' : 'text-gray-400' }}">
                                    {{ $value }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nominal --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nominal Pinjaman</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                        <input
                            type="number"
                            wire:model="amount"
                            placeholder="Contoh: 5000000"
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all"
                        >
                    </div>
                    @error('amount')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach ([1000000, 5000000, 10000000, 25000000] as $nominal)
                            <button type="button"
                                wire:click="$set('amount', {{ $nominal }})"
                                class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-amber-100 hover:text-amber-700 text-gray-600 font-semibold rounded-full transition-colors">
                                Rp {{ number_format($nominal, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tenor --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tenor (Bulan)</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([3, 6, 12, 24, 36, 60] as $bulan)
                            <button type="button"
                                wire:click="$set('duration', {{ $bulan }})"
                                class="text-xs px-4 py-2 border-2 rounded-xl font-bold transition-all
                                    {{ $duration == $bulan
                                        ? 'border-[#e8a838] bg-amber-50 text-amber-700'
                                        : 'border-gray-200 text-gray-500 hover:border-amber-300' }}">
                                {{ $bulan }} Bulan
                            </button>
                        @endforeach
                    </div>
                    @error('duration')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Simulasi Cicilan --}}
                @if ($amount && $duration)
                    @php
                        $bungaPerBulan = 6 / 100 / 12;
                        $cicilan = $amount * $bungaPerBulan / (1 - pow(1 + $bungaPerBulan, -$duration));
                        $total = $cicilan * $duration;
                    @endphp
                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-widest mb-2">Simulasi Cicilan (Bunga 6% p.a.)</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-amber-600 font-semibold">Cicilan/Bulan</p>
                                <p class="text-lg font-extrabold text-amber-800">Rp {{ number_format($cicilan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-amber-600 font-semibold">Total Pembayaran</p>
                                <p class="text-lg font-extrabold text-amber-800">Rp {{ number_format($total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Alasan --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tujuan Penggunaan Dana</label>
                    <textarea
                        wire:model="reason"
                        rows="4"
                        placeholder="Jelaskan tujuan penggunaan dana secara detail (minimal 20 karakter)..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all resize-none"
                    ></textarea>
                    @error('reason')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info --}}
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p class="font-semibold text-sm mb-1">Informasi Penting</p>
                            <p>• Pengajuan akan direview oleh Admin Koperasi terlebih dahulu.</p>
                            <p>• Setelah Admin menyetujui, Manajer Koperasi akan melakukan review final.</p>
                            <p>• Proses review maksimal 3x24 jam.</p>
                            <p>• Bunga pinjaman <span class="font-bold">6% per tahun</span> (flat).</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-[#e8a838] hover:bg-[#d4952f] text-white font-bold py-3 rounded-xl transition-colors text-sm">
                        <span wire:loading.remove wire:target="save">Ajukan Pinjaman</span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm text-center">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>