<?php

use Livewire\Volt\Component;
use App\Models\Loan;

new class extends Component {
    public string $type = '';
    public string $amount = '';
    public string $duration = '';
    public string $reason = '';

    public function submit(): void
    {
        $this->validate([
            'type'     => 'required|in:Pinjaman Usaha,Pinjaman Konsumsi,Pinjaman Darurat',
            'amount'   => 'required|numeric|min:100000',
            'duration' => 'required|integer|min:1|max:60',
            'reason'   => 'required|string|min:10|max:500',
        ], [
            'type.required'     => 'Pilih jenis pinjaman.',
            'amount.min'        => 'Minimal pengajuan adalah Rp 100.000.',
            'duration.required' => 'Isi tenor pinjaman.',
            'reason.min'        => 'Alasan minimal 10 karakter.',
        ]);

        $loan = Loan::create([
            'user_id'  => auth()->id(),
            'type'     => $this->type,
            'amount'   => $this->amount,
            'duration' => $this->duration,
            'reason'   => $this->reason,
            'status'   => 'Baru',
        ]);

        // Create the 5 default pipeline stages
        $stages = [
            'Pengajuan Diterima',
            'Verifikasi Dokumen',
            'Review Kredit',
            'Persetujuan',
            'Dana Dicairkan',
        ];

        foreach ($stages as $order => $stageName) {
            $loan->stages()->create([
                'stage_order' => $order + 1,
                'stage_name'  => $stageName,
                'completed'   => $order === 0, // first stage auto-completed on submission
                'completed_at' => $order === 0 ? now() : null,
            ]);
        }

        // Auto-complete first stage and update progress
        $loan->recalculateProgress();

        session()->flash('success', 'Pengajuan pinjaman Anda berhasil dikirim! ID: ' . $loan->loan_id_number);
        $this->reset(['type', 'amount', 'duration', 'reason']);
    }
}; ?>

<div class="p-6 lg:p-10">
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="font-bold text-sm">Pengajuan Berhasil!</p>
                <p class="text-xs text-emerald-600 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Ajukan Pinjaman Baru</h1>
        <p class="text-gray-500 mt-1 text-sm">Lengkapi formulir di bawah untuk mengajukan pinjaman ke koperasi.</p>
    </div>

    <div class="max-w-2xl bg-white border border-gray-100 rounded-[28px] shadow-sm p-8">
        <form wire:submit="submit" class="space-y-6">

            {{-- Jenis Pinjaman --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jenis Pinjaman <span class="text-red-400">*</span></label>
                <select wire:model="type"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all bg-white">
                    <option value="">-- Pilih Jenis Pinjaman --</option>
                    <option value="Pinjaman Usaha">Pinjaman Usaha</option>
                    <option value="Pinjaman Konsumsi">Pinjaman Konsumsi</option>
                    <option value="Pinjaman Darurat">Pinjaman Darurat</option>
                </select>
                @error('type') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Jumlah Pinjaman --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Pinjaman (Rp) <span class="text-red-400">*</span></label>
                <input type="number" wire:model="amount" placeholder="Contoh: 1000000" min="100000"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all">
                <p class="text-[11px] text-gray-400 mt-1">Minimal Rp 100.000</p>
                @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tenor --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tenor / Durasi (Bulan) <span class="text-red-400">*</span></label>
                <input type="number" wire:model="duration" placeholder="Contoh: 12" min="1" max="60"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all">
                @error('duration') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alasan Pinjaman <span class="text-red-400">*</span></label>
                <textarea wire:model="reason" rows="4" placeholder="Jelaskan secara singkat tujuan pinjaman Anda..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#e8a838] focus:ring-2 focus:ring-amber-100 transition-all resize-none"></textarea>
                @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('pinjaman.tracking') }}" wire:navigate
                    class="px-6 py-2.5 border border-gray-200 text-gray-600 font-bold text-sm rounded-xl hover:bg-gray-50 transition-colors">
                    Lihat Status
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="px-8 py-2.5 bg-[#e8a838] hover:bg-[#d4952f] text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-amber-100 flex items-center gap-2">
                    <span wire:loading.remove>Kirim Pengajuan</span>
                    <span wire:loading>Mengirim...</span>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 max-w-2xl p-4 bg-amber-50 border border-amber-100 rounded-xl">
        <p class="text-sm text-amber-800">
            <strong>Catatan:</strong> Persetujuan bergantung pada saldo kas koperasi dan riwayat simpanan Anda. Proses peninjauan memakan waktu 1–3 hari kerja.
        </p>
    </div>
</div>