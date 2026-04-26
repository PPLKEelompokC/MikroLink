<?php

use Livewire\Volt\Component;
use App\Models\Loan;

new class extends Component {
    public $amount;
    public $duration;
    public $reason;

    public function submit()
    {
        // Validasi input
        $this->validate([
            'amount' => 'required|numeric|min:100000',
            'duration' => 'required|integer|min:1|max:60',
            'reason' => 'required|string|min:10|max:255',
        ]);

        // Simpan data ke database
        Loan::create([
            'user_id' => auth()->id(),
            'amount' => $this->amount,
            'duration' => $this->duration,
            'reason' => $this->reason,
            'status' => 'Pending',
        ]);

        // Berikan notifikasi sukses menggunakan session flash
        session()->flash('success', 'Pengajuan pinjaman Anda berhasil dikirim dan menunggu validasi admin.');
        
        // Reset form
        $this->reset();
    }
}; ?>

<div class="p-6 lg:p-10">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="mb-8">
        <flux:heading size="xl" level="1">Ajukan Pinjaman Baru</flux:heading>
        <flux:subheading>Silakan lengkapi formulir di bawah ini untuk mengajukan pinjaman ke koperasi.</flux:subheading>
    </div>

    {{-- Card Formulir --}}
    <flux:card class="max-w-2xl bg-white shadow-sm border-zinc-200">
        <form wire:submit="submit" class="space-y-6">
            
            {{-- Input Jumlah Pinjaman --}}
            <flux:input 
                wire:model="amount" 
                label="Jumlah Pinjaman (Rp)" 
                type="number" 
                placeholder="Contoh: 1000000" 
                description="Minimal pengajuan adalah Rp 100.000"
            />

            {{-- Input Tenor --}}
            <flux:input 
                wire:model="duration" 
                label="Tenor / Durasi (Bulan)" 
                type="number" 
                placeholder="Contoh: 12" 
                description="Lama waktu pelunasan dalam satuan bulan"
            />

            {{-- Input Alasan --}}
            <flux:textarea 
                wire:model="reason" 
                label="Alasan Pinjaman" 
                placeholder="Jelaskan secara singkat tujuan pinjaman Anda..." 
                rows="4"
            />

            {{-- Tombol Submit --}}
            <div class="flex items-center gap-4">
                <flux:spacer />
                <flux:button 
                    type="submit" 
                    variant="primary" 
                    class="bg-[#e8a838] hover:bg-[#d4952f] border-none text-white font-bold px-8"
                >
                    Kirim Pengajuan
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Informasi Tambahan --}}
    <div class="mt-8 max-w-2xl p-4 bg-amber-50 border border-amber-100 rounded-lg">
        <p class="text-sm text-amber-800">
            <strong>Catatan:</strong> Persetujuan pinjaman bergantung pada ketersediaan saldo kas koperasi dan riwayat simpanan Anda. Proses peninjauan biasanya memakan waktu 1-3 hari kerja.
        </p>
    </div>
</div>