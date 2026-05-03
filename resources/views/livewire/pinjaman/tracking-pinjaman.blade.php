<?php

use Livewire\Volt\Component;
use App\Models\Loan;

new class extends Component {

    public ?Loan $selectedLoan = null;

    public function with(): array
    {
        return [
            'loans' => Loan::with(['stages'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get(),
        ];
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedLoan = Loan::with(['stages.completedBy'])->findOrFail($id);
    }

    public function tutupModal(): void
    {
        $this->selectedLoan = null;
    }
}; ?>

<div>
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Status Pinjaman Saya</h3>
                    <p class="text-[11px] text-gray-400">Lacak progress pencairan dana Anda</p>
                </div>
            </div>
            <a href="{{ route('pinjaman.ajukan') }}" wire:navigate
                class="px-4 py-1.5 bg-[#e8a838] hover:bg-[#d4952f] text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajukan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-8 py-4">ID Pinjaman</th>
                        <th class="px-8 py-4">Jenis</th>
                        <th class="px-8 py-4">Jumlah</th>
                        <th class="px-8 py-4 text-center">Status</th>
                        <th class="px-8 py-4">Progress</th>
                        <th class="px-8 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($loans as $loan)
                        @php
                            $statusConfig = [
                                'Baru'         => 'bg-blue-50 text-blue-600',
                                'Dalam Review' => 'bg-amber-50 text-amber-600',
                                'Disetujui'    => 'bg-emerald-50 text-emerald-600',
                                'Ditolak'      => 'bg-red-50 text-red-600',
                            ][$loan->status] ?? 'bg-gray-50 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-gray-800">{{ $loan->loan_id_number }}</span>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-600">{{ $loan->type }}</td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-extrabold text-gray-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold {{ $statusConfig }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2 min-w-[100px]">
                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-[#e8a838] rounded-full transition-all duration-700"
                                            style="width: {{ $loan->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 w-8 text-right">{{ $loan->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <button type="button" wire:click="lihatDetail({{ $loan->id }})"
                                    class="inline-flex items-center gap-1 text-[#e8a838] hover:text-[#d4952f] text-xs font-bold transition-colors">
                                    Lihat
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-gray-400 italic text-sm">
                                Belum ada pengajuan pinjaman.
                                <a href="{{ route('pinjaman.ajukan') }}" class="text-[#e8a838] font-bold hover:underline ml-1">Ajukan sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Timeline (Read-only) --}}
    @if ($selectedLoan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="tutupModal"></div>
            <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-md overflow-hidden z-10"
                x-data x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Status Pencairan Dana</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $selectedLoan->loan_id_number }}</p>
                    </div>
                    <button wire:click="tutupModal" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
                    {{-- Summary --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jumlah</p>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($selectedLoan->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            <p class="text-sm font-bold text-gray-900">{{ $selectedLoan->status }}</p>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Progress</span>
                            <span class="text-xs font-bold text-[#e8a838]">{{ $selectedLoan->progress_percentage }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#e8a838] rounded-full" style="width: {{ $selectedLoan->progress_percentage }}%"></div>
                        </div>
                    </div>

                    {{-- Vertical Timeline --}}
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-4">Riwayat Tahapan</p>
                        <div class="relative">
                            <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-gray-200"></div>
                            <div class="space-y-5">
                                @foreach ($selectedLoan->stages->sortBy('stage_order') as $stage)
                                    <div class="flex gap-4 relative">
                                        <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center mt-0.5 z-10
                                            {{ $stage->completed ? 'bg-teal-500 border-teal-500' : 'bg-white border-gray-300' }}">
                                            @if ($stage->completed)
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 pb-1">
                                            <p class="text-sm font-bold {{ $stage->completed ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $stage->stage_name }}
                                            </p>
                                            @if ($stage->completed)
                                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $stage->completed_at?->translatedFormat('d M Y, H:i') }}</p>
                                            @else
                                                <p class="text-[11px] text-gray-300 mt-0.5">Menunggu verifikasi</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    <button type="button" wire:click="tutupModal"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition-colors text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
