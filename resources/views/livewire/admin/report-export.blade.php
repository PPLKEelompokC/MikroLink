<?php

use Livewire\Volt\Component;
use App\Services\MultiFormatExportService;

new class extends Component {
    public $startDate;
    public $endDate;
    public $format = 'xlsx';
    public $showModal = false;

    public function mount()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function export(MultiFormatExportService $exportService)
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'format' => 'required|in:xlsx,csv',
        ]);

        $this->showModal = false;
        
        return $exportService->export($this->startDate, $this->endDate, $this->format);
    }
}; ?>

<div>
    <button @click="$wire.set('showModal', true)" 
            class="inline-flex items-center gap-2 bg-[#ffa200] hover:bg-orange-500 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-orange-100 hover:scale-105 active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Ekspor Laporan
    </button>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white w-full max-w-md rounded-[32px] shadow-2xl p-8 overflow-hidden relative border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h4 class="text-xl font-extrabold text-gray-900">Ekspor Data Koperasi</h4>
                        <p class="text-sm text-gray-400 font-medium mt-1">Pilih rentang waktu & format file</p>
                    </div>
                    <button @click="$wire.set('showModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="export" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest px-1">Dari Tanggal</label>
                            <input type="date" wire:model="startDate" 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:border-[#ffa200] transition-all">
                            @error('startDate') <span class="text-[10px] text-red-500 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest px-1">Sampai Tanggal</label>
                            <input type="date" wire:model="endDate" 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:border-[#ffa200] transition-all">
                            @error('endDate') <span class="text-[10px] text-red-500 font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest px-1">Format File</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-2xl cursor-pointer transition-all hover:bg-orange-50/50 group"
                                   :class="{'ring-2 ring-[#ffa200] border-transparent bg-orange-50': $wire.format === 'xlsx'}">
                                <input type="radio" wire:model.live="format" value="xlsx" class="sr-only">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-bold text-gray-700" :class="{'text-orange-600': $wire.format === 'xlsx'}">Excel (.xlsx)</span>
                                    <span class="text-[9px] text-gray-400 font-medium uppercase tracking-tighter">Multi Sheet</span>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-2xl cursor-pointer transition-all hover:bg-orange-50/50 group"
                                   :class="{'ring-2 ring-[#ffa200] border-transparent bg-orange-50': $wire.format === 'csv'}">
                                <input type="radio" wire:model.live="format" value="csv" class="sr-only">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-bold text-gray-700" :class="{'text-orange-600': $wire.format === 'csv'}">CSV (.csv)</span>
                                    <span class="text-[9px] text-gray-400 font-medium uppercase tracking-tighter">Flat Data</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-2xl flex gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-[11px] text-blue-700 leading-relaxed font-medium">
                            Data sensitif anggota (NIK) akan disamarkan secara otomatis untuk menjaga privasi.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-4 bg-[#ffa200] text-white font-bold rounded-2xl shadow-lg shadow-orange-100 hover:bg-orange-500 transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove>Generate & Download</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
