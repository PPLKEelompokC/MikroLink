<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Withdrawal;

new #[Layout('layouts.app')] class extends Component {

    public $amount;
    public string $bank_name = '';
    public string $bank_account = '';
    public string $bank_account_name = '';

    public function with(): array
    {
        $user = auth()->user();
        $availableBalance = $user->availableSukarelaBalance();
        $withdrawals = $user->withdrawals()->latest()->get();

        return [
            'availableBalance' => $availableBalance,
            'withdrawals' => $withdrawals,
        ];
    }

    public function submit(): void
    {
        $user = auth()->user();
        $availableBalance = $user->availableSukarelaBalance();

        $this->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:' . $availableBalance],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:30'],
            'bank_account_name' => ['required', 'string', 'max:150'],
        ], [
            'amount.required' => 'Nominal penarikan wajib diisi.',
            'amount.min' => 'Minimal penarikan adalah Rp 10.000.',
            'amount.max' => 'Nominal melebihi saldo tersedia (Rp ' . number_format($availableBalance, 0, ',', '.') . ').',
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_account.required' => 'Nomor rekening wajib diisi.',
            'bank_account_name.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $this->amount,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_account_name' => $this->bank_account_name,
            'status' => 'PENDING',
        ]);

        $this->reset(['amount', 'bank_name', 'bank_account', 'bank_account_name']);
        $this->dispatch('penarikan-berhasil');
    }
};
?>

<div>
    {{-- Toast Notifikasi --}}
    <div
        x-data="{ show: false }"
        x-on:penarikan-berhasil.window="show = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 2500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-emerald-200 shadow-xl rounded-2xl px-5 py-4 max-w-sm"
        style="display: none;"
    >
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-gray-900 text-sm">Penarikan Berhasil Diajukan!</p>
            <p class="text-xs text-gray-500 mt-0.5">Mengalihkan ke dashboard...</p>
        </div>
    </div>

    {{-- Konten Halaman --}}
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

        {{-- Saldo Tersedia Card --}}
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-6 rounded-[24px] text-white flex items-center justify-between mb-6">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Saldo Simpanan Sukarela Tersedia</p>
                <p class="text-3xl font-extrabold mt-2">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        {{-- Card Form Penarikan --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-8">

            {{-- Header Card --}}
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Formulir Penarikan Simpanan</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Isi nominal dan informasi rekening bank tujuan transfer.</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form wire:submit="submit" class="p-8 space-y-6">

                {{-- Nominal Penarikan --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nominal Penarikan</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                        <input
                            type="number"
                            wire:model="amount"
                            placeholder="Contoh: 50000"
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"
                        >
                    </div>
                    @error('amount')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach ([50000, 100000, 250000, 500000] as $nominal)
                            <button type="button"
                                wire:click="$set('amount', {{ $nominal }})"
                                class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-red-100 hover:text-red-700 text-gray-600 font-semibold rounded-full transition-colors">
                                Rp {{ number_format($nominal, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Informasi Rekening Bank --}}
                <div class="space-y-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="text-sm font-bold text-gray-700">Informasi Rekening Bank Tujuan</span>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Bank</label>
                        <select
                            wire:model="bank_name"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all bg-white">
                            <option value="">Pilih bank...</option>
                            @foreach (['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI', 'CIMB Niaga', 'Danamon', 'Permata', 'OCBC NISP', 'BTN'] as $bank)
                                <option value="{{ $bank }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                        @error('bank_name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Rekening</label>
                        <input
                            type="text"
                            wire:model="bank_account"
                            placeholder="Contoh: 1234567890"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"
                        >
                        @error('bank_account')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pemilik Rekening</label>
                        <input
                            type="text"
                            wire:model="bank_account_name"
                            placeholder="Contoh: BUDI SANTOSO"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"
                        >
                        @error('bank_account_name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-xs text-amber-800 space-y-1">
                            <p class="font-semibold text-sm mb-1">Informasi Penting</p>
                            <p>• Hanya simpanan <span class="font-bold">Sukarela</span> yang dapat ditarik.</p>
                            <p>• Penarikan diproses pengurus maksimal <span class="font-bold">2×24 jam</span>.</p>
                            <p>• Minimal penarikan <span class="font-bold">Rp 10.000</span>.</p>
                            <p>• Pastikan informasi rekening bank sudah benar.</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-colors shadow-sm shadow-red-100 text-sm">
                        <span wire:loading.remove wire:target="submit">Ajukan Penarikan</span>
                        <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
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

        {{-- Riwayat Penarikan --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Riwayat Penarikan</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Tanggal</th>
                            <th class="px-8 py-4">Nominal</th>
                            <th class="px-8 py-4">Rekening Tujuan</th>
                            <th class="px-8 py-4 text-center">Status</th>
                            <th class="px-8 py-4">Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($withdrawals as $wd)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-sm text-gray-500">{{ $wd->created_at->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $wd->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-extrabold text-gray-900">
                                        Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-gray-900">{{ $wd->bank_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $wd->bank_account }} — {{ $wd->bank_account_name }}</p>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusColor = [
                                            'PENDING'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'APPROVED' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'REJECTED' => 'bg-red-50 text-red-600 border-red-100',
                                        ][$wd->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold border {{ $statusColor }} uppercase tracking-widest">
                                        {{ $wd->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs text-gray-500 italic">
                                        {{ $wd->admin_note ?? '-' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic text-sm">
                                    Belum ada riwayat penarikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
