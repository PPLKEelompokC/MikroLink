<?php

use App\Models\Koperasi;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $namaKoperasi = '';
    public string $alamat = '';
    public string $amount = '';
    public string $type = 'hibah';
    public string $notes = '';
    public float $saldoKas = 0;
    public ?string $koperasiId = null;

    public function mount(): void
    {
        $koperasi = Koperasi::firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
        );

        $this->koperasiId = $koperasi->id_koperasi;
        $this->namaKoperasi = $koperasi->nama_koperasi;
        $this->alamat = $koperasi->alamat;
        $this->saldoKas = $koperasi->saldo_kas;
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'namaKoperasi' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
        ]);

        $koperasi = Koperasi::find($this->koperasiId);
        if ($koperasi) {
            $koperasi->update([
                'nama_koperasi' => $validated['namaKoperasi'],
                'alamat' => $validated['alamat'],
            ]);
        }

        $this->dispatch('profile-updated');
    }

    public function adjustCapital(): void
    {
        $validated = $this->validate([
            'amount' => ['required', 'numeric'],
            'type'   => ['required', 'string'],
            'notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $koperasi = Koperasi::find($this->koperasiId);
        if ($koperasi) {
            $koperasi->updateSaldo((float) $validated['amount'], $validated['type'], null, null, $validated['notes']);
            $this->saldoKas = $koperasi->saldo_kas;
        }

        $this->amount = '';
        $this->notes = '';
        $this->type = 'hibah';
        $this->dispatch('notif', type: 'success', message: 'Saldo Kas berhasil disesuaikan.');
    }
}; ?>

<div>
    {{-- Toast Notifikasi --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notif.window="
            message = $event.detail.message;
            type = $event.detail.type;
            show = true;
            setTimeout(() => show = false, 3000)
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border shadow-xl rounded-2xl px-5 py-4 max-w-sm"
        :class="type === 'success' ? 'border-emerald-200' : 'border-red-200'"
        style="display: none;"
    >
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
            :class="type === 'success' ? 'bg-emerald-100' : 'bg-red-100'">
            <svg x-show="type === 'success'" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg x-show="type === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-gray-800" x-text="message"></p>
    </div>

    <div class="w-full max-w-4xl mx-auto px-10 py-12 relative z-10">
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Profil Koperasi</h1>
                <livewire:admin.report-export />
            </div>

            <form wire:submit="updateProfile" class="space-y-6">
                <div>
                    <label for="namaKoperasi" class="block text-sm font-bold text-gray-700 mb-1">Nama Koperasi</label>
                    <input type="text" wire:model="namaKoperasi" id="namaKoperasi" required
                        class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                    @error('namaKoperasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-bold text-gray-700 mb-1">Alamat</label>
                    <textarea wire:model="alamat" id="alamat" rows="3" required
                        class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm"></textarea>
                    @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center pt-4">
                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Profil Koperasi berhasil diperbarui.') }}
                    </x-action-message>
                    
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg mr-4 hover:bg-gray-200">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-[#e8a838] text-white font-bold rounded-lg shadow-md hover:bg-[#ffa200] transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <hr class="my-10 border-gray-200">

            <h2 class="text-xl font-bold text-gray-900 mb-6">Penyesuaian Saldo Kas</h2>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6 flex justify-between items-center">
                <div>
                    <span class="text-sm font-bold text-gray-600">Saldo Saat Ini</span>
                    <div class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</div>
                </div>
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>

            <form wire:submit="adjustCapital" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-bold text-gray-700 mb-1">Kategori Modal</label>
                        <select wire:model="type" id="type" required
                            class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                            <option value="hibah">Hibah / Dana Eksternal</option>
                            <option value="dana_cadangan">Dana Cadangan</option>
                            <option value="pinjaman_usaha">Modal Usaha / Kredit</option>
                            <option value="penyesuaian_modal">Koreksi Pembukuan</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ 
                        displayAmount: '',
                        rawAmount: @entangle('amount')
                    }" x-init="
                        $watch('rawAmount', value => {
                            if (value === '' || value === null) {
                                displayAmount = '';
                            }
                        })
                    ">
                        <label for="amount" class="block text-sm font-bold text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input type="text" id="amount" 
                            x-model="displayAmount"
                            @input="
                                let val = displayAmount.replace(/[^0-9-]/g, '');
                                if (val.indexOf('-') > 0) val = val.replace(/-/g, '');
                                if ((val.match(/-/g) || []).length > 1) val = '-' + val.replace(/-/g, '');
                                
                                rawAmount = val;
                                displayAmount = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            "
                            required placeholder="Contoh: 1.000.000 atau -500.000"
                            class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Gunakan nilai negatif (-) untuk mengurangi saldo.</p>
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-1">Keterangan / Catatan</label>
                    <input type="text" wire:model="notes" id="notes" placeholder="Contoh: Koreksi saldo bulan Mei..."
                        class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center pt-4 border-t border-gray-100">
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow-md hover:bg-emerald-700 transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="adjustCapital">Sesuaikan Saldo</span>
                        <span wire:loading wire:target="adjustCapital">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>