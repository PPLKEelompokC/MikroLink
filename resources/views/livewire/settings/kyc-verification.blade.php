<?php

use App\Models\CommunityDocument;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $ktp_photo;
    public $nik = '';
    public $fullName = '';
    public $status = 'Belum Verifikasi';
    public $isProcessing = false;
    public $note = '';

    public function mount()
    {
        $doc = CommunityDocument::where('user_id', Auth::id())
            ->where('document_name', 'KTP')
            ->first();

        if ($doc) {
            if ($doc->status === 'approved') {
                $this->status = 'Terverifikasi';
            } elseif ($doc->status === 'rejected') {
                $this->status = 'Ditolak';
                $this->note = $doc->note;
            } else {
                $this->status = 'Sedang Diproses';
            }
            
            // Placeholder data if already uploaded
            $this->nik = '327301XXXXXXXXXX';
            $this->fullName = Auth::user()->name;
        }
    }

    public function uploadKtp()
    {
        $this->validate([
            'ktp_photo' => 'required|image|max:2048',
        ]);

        $this->isProcessing = true;

        // OCR Simulation: Dynamic based on user or randomized for testing
        $this->nik = '3171' . rand(100000000000, 999999999999);
        $this->fullName = Auth::user()->name; 

        // Real Upload to Private Storage
        $path = $this->ktp_photo->store('kyc-docs', 'local');

        // Save to DB
        CommunityDocument::updateOrCreate(
            ['user_id' => Auth::id(), 'document_name' => 'KTP'],
            [
                'file_path' => $path,
                'status' => 'pending',
                'note' => 'Unggahan KYC Digital',
            ]
        );

        $this->status = 'Sedang Diproses';
        $this->isProcessing = false;

        $this->dispatch('kyc-updated');
    }
}; ?>

<div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Verifikasi Identitas (KYC)</h3>
                <p class="text-sm text-gray-500">Unggah KTP Anda untuk memverifikasi keanggotaan.</p>
            </div>
            @php
                $statusClasses = [
                    'Belum Verifikasi' => 'bg-gray-100 text-gray-600',
                    'Sedang Diproses' => 'bg-amber-100 text-amber-700',
                    'Terverifikasi' => 'bg-emerald-100 text-emerald-700',
                    'Ditolak' => 'bg-red-100 text-red-700',
                ];
                $currentClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <span class="px-4 py-1.5 rounded-full text-[11px] font-extrabold uppercase tracking-widest {{ $currentClass }}">
                {{ $status }}
            </span>
        </div>
    </div>

    <div class="p-8">
        @if($status === 'Ditolak')
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-xs font-bold text-red-700 uppercase tracking-wider">Alasan Penolakan:</p>
                    <p class="text-sm text-red-600 mt-1 italic">"{{ $note }}"</p>
                </div>
            </div>
        @endif

        @if($status === 'Belum Verifikasi' || $status === 'Ditolak')
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-3xl p-10 bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                <input type="file" wire:model="ktp_photo" class="hidden" id="ktp_upload" accept="image/*">
                
                @if ($ktp_photo)
                    <div class="relative w-full max-w-xs mb-4">
                        <img src="{{ $ktp_photo->temporaryUrl() }}" class="rounded-2xl shadow-lg border-4 border-white">
                        <button wire:click="$set('ktp_photo', null)" class="absolute -top-3 -right-3 bg-red-500 text-white p-1.5 rounded-full shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @else
                    <label for="ktp_upload" class="cursor-pointer flex flex-col items-center">
                        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Ambil Foto E-KTP</span>
                        <span class="text-xs text-gray-400 mt-1">Format: JPG, PNG (Maks. 2MB)</span>
                    </label>
                @endif

                @if ($ktp_photo)
                    <button wire:click="uploadKtp" wire:loading.attr="disabled" class="mt-6 px-8 py-3 bg-amber-500 text-white font-bold rounded-2xl hover:bg-amber-600 shadow-lg shadow-amber-100 transition-all flex items-center gap-2">
                        <span wire:loading.remove wire:target="uploadKtp">Verifikasi Sekarang</span>
                        <span wire:loading wire:target="uploadKtp">Memproses OCR...</span>
                        <svg wire:loading.remove wire:target="uploadKtp" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hasil Pembacaan KTP (OCR)</h4>
                        <span class="text-[9px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded-md font-bold">SILAKAN KOREKSI JIKA SALAH</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1 block">NIK</label>
                            <input type="text" wire:model="nik" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold text-gray-900 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1 block">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" wire:model="fullName" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold text-gray-900 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-3 text-amber-600 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-bold">Informasi</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Data di atas adalah hasil pembacaan otomatis dari foto KTP Anda. Mohon tunggu tim admin melakukan validasi akhir untuk mengaktifkan status verifikasi penuh.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
