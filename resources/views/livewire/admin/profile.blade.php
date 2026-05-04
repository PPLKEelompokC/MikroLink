<?php

use App\Models\Koperasi;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $namaKoperasi = '';
    public string $alamat = '';
    public string $amount = '';
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
        ]);

        $koperasi = Koperasi::find($this->koperasiId);
        if ($koperasi) {
            $koperasi->updateSaldo((float) $validated['amount']);
            $this->saldoKas = $koperasi->saldo_kas;
        }

        $this->amount = '';
        $this->dispatch('capital-updated');
    }
}; ?>

<div>
    @include('components.navbar')

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
                <div>
                    <label for="amount" class="block text-sm font-bold text-gray-700 mb-1">Jumlah Penyesuaian (Rp)</label>
                    <p class="text-xs text-gray-500 mb-2">Gunakan nilai negatif (contoh: -500000) untuk mengurangi saldo.</p>
                    <input type="number" wire:model="amount" id="amount" step="0.01" required placeholder="Contoh: 1000000 atau -500000"
                        class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center pt-4">
                    <x-action-message class="me-3" on="capital-updated">
                        {{ __('Saldo Kas berhasil disesuaikan.') }}
                    </x-action-message>
                    
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow-md hover:bg-emerald-700 transition-colors">
                        Sesuaikan Saldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>