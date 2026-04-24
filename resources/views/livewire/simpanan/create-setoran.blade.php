<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Deposit;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public $amount;
    public $type = 'SUKARELA';
    public $proof;

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric|min:10000',
            'type'   => 'required|in:POKOK,WAJIB,SUKARELA',
            'proof'  => 'required|image|max:2048',
        ], [
            'amount.required' => 'Nominal setoran wajib diisi.',
            'amount.min'      => 'Minimal setoran adalah Rp 10.000.',
            'proof.required'  => 'Wajib mengunggah bukti transfer.',
            'proof.image'     => 'File harus berupa gambar (JPG/PNG).',
            'proof.max'       => 'Ukuran file maksimal 2MB.',
        ]);

        $path = $this->proof->store('deposits-proof', 'public');

        Deposit::create([
            'user_id'    => auth()->id(),
            'amount'     => $this->amount,
            'type'       => $this->type,
            'proof_path' => $path,
            'status'     => 'PENDING',
        ]);

        $this->dispatch('setoran-berhasil');
    }
};
?>

<div>
    {{-- Toast Notifikasi --}}
    <div
        x-data="{ show: false }"
        x-on:setoran-berhasil.window="show = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 2500)"
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
            <p class="font-bold text-gray-900 text-sm">Setoran Berhasil Diajukan!</p>
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

        {{-- Card Utama --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">

            {{-- Header Card --}}
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Formulir Setoran Simpanan</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Isi nominal dan unggah bukti transfer bank Anda.</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form wire:submit="save" class="p-8 space-y-6">

                {{-- Nominal Setoran --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nominal Setoran</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                        <input
                            type="number"
                            wire:model="amount"
                            placeholder="Contoh: 50000"
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all"
                        >
                    </div>
                    @error('amount')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach ([50000, 100000, 250000, 500000] as $nominal)
                            <button type="button"
                                wire:click="$set('amount', {{ $nominal }})"
                                class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-600 font-semibold rounded-full transition-colors">
                                Rp {{ number_format($nominal, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Jenis Simpanan --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Jenis Simpanan</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['POKOK' => ['Pokok', 'Simpanan Pokok'], 'WAJIB' => ['Wajib', 'Simpanan Wajib'], 'SUKARELA' => ['Sukarela', 'Simpanan Sukarela']] as $value => [$short, $label])
                            <button
                                type="button"
                                wire:click="$set('type', '{{ $value }}')"
                                class="flex flex-col items-center justify-center p-4 border-2 rounded-xl transition-all
                                    {{ $type === $value
                                        ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                        : 'border-gray-200 text-gray-500 hover:border-emerald-300 hover:bg-emerald-50/50' }}">
                                <span class="font-bold text-sm">{{ $short }}</span>
                                <span class="text-[10px] font-normal mt-0.5 {{ $type === $value ? 'text-emerald-500' : 'text-gray-400' }}">
                                    {{ $label }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Bukti Transfer --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Bukti Transfer</label>
                    <label for="proof-upload"
                        class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed rounded-xl cursor-pointer transition-all
                            {{ $proof ? 'border-emerald-400 bg-emerald-50' : 'border-gray-300 hover:border-emerald-400 hover:bg-emerald-50/40' }}">
                        <input type="file" id="proof-upload" wire:model="proof" accept="image/*" class="hidden">
                        <svg class="w-10 h-10 mb-2 {{ $proof ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium {{ $proof ? 'text-emerald-700' : 'text-gray-500' }}">
                            {{ $proof ? $proof->getClientOriginalName() : 'Klik untuk upload bukti transfer' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG — Maks. 2MB</p>
                    </label>

                    <div wire:loading wire:target="proof" class="flex items-center gap-2 text-blue-600 text-xs font-semibold">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        Mengunggah gambar...
                    </div>

                    @if ($proof)
                        <div wire:loading.remove wire:target="proof" class="mt-3 p-3 bg-gray-50 border border-gray-100 rounded-xl">
                            <p class="text-xs text-gray-500 mb-2 font-semibold">Preview:</p>
                            <img src="{{ $proof->temporaryUrl() }}" class="max-h-48 rounded-lg shadow-sm mx-auto object-cover">
                        </div>
                    @endif

                    @error('proof')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p class="font-semibold text-sm mb-1">Informasi Penting</p>
                            <p>• Pastikan bukti transfer jelas dan terbaca.</p>
                            <p>• Setoran divalidasi admin maksimal 1×24 jam.</p>
                            <p>• Minimal setoran <span class="font-bold">Rp 10.000</span>.</p>
                            <p>• Transfer ke: <span class="font-bold">BNI 1234567890 a.n Koperasi XYZ</span>.</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors shadow-sm shadow-emerald-100 text-sm">
                        <span wire:loading.remove wire:target="save">Kirim Setoran</span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
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
    </div>
</div>