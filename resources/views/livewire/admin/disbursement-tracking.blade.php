<?php

use Livewire\Volt\Component;
use App\Models\Loan;
use App\Models\LoanStage;

new class extends Component {

    public ?Loan $selectedLoan = null;
    public string $filterStatus = '';
    public string $sortOrder = 'asc';
    public string $exportMonth = '';
    public string $stageNote = '';

    public function mount(): void
    {
        $this->exportMonth = now()->format('Y-m');
    }

    public function with(): array
    {
        $loans = Loan::with(['user', 'stages'])
            ->when($this->filterStatus !== '', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('loan_id_number', $this->sortOrder)
            ->get();

        return ['loans' => $loans];
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedLoan = Loan::with(['stages.completedBy', 'user'])->findOrFail($id);
        $this->stageNote = '';
    }

    public function tutupModal(): void
    {
        $this->selectedLoan = null;
        $this->stageNote = '';
    }

    public function advanceStage(int $loanId): void
    {
        $loan = Loan::with('stages')->findOrFail($loanId);

        $nextStage = $loan->stages
            ->where('completed', false)
            ->sortBy('stage_order')
            ->first();

        if (! $nextStage) {
            $this->dispatch('notif', type: 'info', message: 'Semua tahapan sudah selesai.');
            return;
        }

        $nextStage->update([
            'completed'       => true,
            'completed_at'    => now(),
            'completed_by_id' => auth()->id(),
            'notes'           => $this->stageNote ?: null,
        ]);

        $loan->recalculateProgress();

        $this->selectedLoan = Loan::with(['stages.completedBy', 'user'])->findOrFail($loanId);
        $this->stageNote = '';

        $this->dispatch('notif', type: 'success', message: 'Tahapan "' . $nextStage->stage_name . '" berhasil diverifikasi!');
    }

    public function rejectLoan(int $loanId): void
    {
        $loan = Loan::findOrFail($loanId);
        $loan->update(['status' => 'Ditolak', 'notes' => $this->stageNote ?: 'Pengajuan ditolak oleh admin.']);
        $this->tutupModal();
        $this->dispatch('notif', type: 'error', message: 'Pengajuan pinjaman telah ditolak.');
    }

    public function exportPrint(): void
    {
        $this->dispatch('print-disbursement', month: $this->exportMonth);
    }
}; ?>

<div>
    {{-- Toast --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notif.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed top-6 right-6 z-[100] flex items-center gap-3 bg-white border shadow-xl rounded-2xl px-5 py-4 max-w-sm"
        :class="type === 'success' ? 'border-emerald-200' : (type === 'error' ? 'border-red-200' : 'border-blue-200')"
        style="display:none;"
    >
        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
            :class="type === 'success' ? 'bg-emerald-100' : (type === 'error' ? 'bg-red-100' : 'bg-blue-100')">
            <svg x-show="type === 'success'" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="type === 'error'" class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <svg x-show="type === 'info'" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-800" x-text="message"></p>
    </div>

    {{-- Card Container --}}
    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-[16px] font-bold text-gray-900">Progress Tracking Operasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Status aplikasi pinjaman terbaru</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Sort Filter --}}
                    <select wire:model.live="sortOrder"
                        class="text-xs font-semibold border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-700 focus:outline-none focus:border-[#e8a838] cursor-pointer">
                        <option value="asc">ID: A → Z</option>
                        <option value="desc">ID: Z → A</option>
                    </select>

                    {{-- Status Filter --}}
                    <select wire:model.live="filterStatus"
                        class="text-xs font-semibold border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-700 focus:outline-none focus:border-[#e8a838] cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Baru">Baru</option>
                        <option value="Dalam Review">Dalam Review</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>

                    {{-- Export --}}
                    <div class="flex items-center gap-1">
                        <input type="month" wire:model="exportMonth"
                            class="text-xs font-semibold border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-700 focus:outline-none focus:border-[#e8a838]">
                        <button wire:click="exportPrint"
                            class="px-4 py-2 bg-[#e8a838] hover:bg-[#d4952f] text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto" id="disbursement-print-area">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">ID Pinjaman</th>
                        <th class="px-6 py-4">Anggota</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Jumlah</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Progress</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($loans as $loan)
                        @php
                            $statusConfig = [
                                'Baru'        => 'bg-blue-100 text-blue-700',
                                'Dalam Review' => 'bg-amber-100 text-amber-700',
                                'Disetujui'   => 'bg-amber-400/20 text-amber-600',
                                'Ditolak'     => 'bg-red-100 text-red-600',
                            ][$loan->status] ?? 'bg-gray-100 text-gray-600';

                            $progressColor = $loan->progress_percentage >= 100
                                ? 'bg-emerald-500'
                                : ($loan->progress_percentage >= 50 ? 'bg-[#e8a838]' : 'bg-blue-400');
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-800">{{ $loan->loan_id_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 uppercase">
                                        {{ substr($loan->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-700 font-medium">{{ $loan->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $loan->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold {{ $statusConfig }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 min-w-[100px]">
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $progressColor }} rounded-full transition-all duration-700"
                                            style="width: {{ $loan->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 w-8 text-right">{{ $loan->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="lihatDetail({{ $loan->id }})"
                                    class="inline-flex items-center gap-1 text-[#e8a838] hover:text-[#d4952f] text-xs font-bold transition-colors">
                                    Lihat
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400 text-sm italic">
                                Belum ada data pinjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail + Timeline --}}
    @if ($selectedLoan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="modal-{{ $selectedLoan->id }}">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="tutupModal"></div>

            <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-lg overflow-hidden z-10"
                x-data x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Detail Pinjaman</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $selectedLoan->loan_id_number }}</p>
                    </div>
                    <button wire:click="tutupModal" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 max-h-[75vh] overflow-y-auto space-y-5">
                    {{-- Info Summary --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Anggota</p>
                            <p class="text-sm font-bold text-gray-900">{{ $selectedLoan->user->name }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jumlah</p>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($selectedLoan->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis</p>
                            <p class="text-sm font-medium text-gray-700">{{ $selectedLoan->type }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tenor</p>
                            <p class="text-sm font-medium text-gray-700">{{ $selectedLoan->duration }} Bulan</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Progress Pencairan</span>
                            <span class="text-xs font-bold text-[#e8a838]">{{ $selectedLoan->progress_percentage }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#e8a838] rounded-full transition-all duration-700"
                                style="width: {{ $selectedLoan->progress_percentage }}%"></div>
                        </div>
                    </div>

                    {{-- Vertical Timeline --}}
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-4">Status Tahapan</p>
                        <div class="relative">
                            {{-- vertical line --}}
                            <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-gray-200"></div>

                            <div class="space-y-5">
                                @foreach ($selectedLoan->stages->sortBy('stage_order') as $stage)
                                    <div class="flex gap-4 relative">
                                        {{-- Dot --}}
                                        <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center mt-0.5 z-10
                                            {{ $stage->completed
                                                ? 'bg-teal-500 border-teal-500'
                                                : 'bg-white border-gray-300' }}">
                                            @if ($stage->completed)
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                        {{-- Content --}}
                                        <div class="flex-1 pb-1">
                                            <p class="text-sm font-bold {{ $stage->completed ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $stage->stage_name }}
                                            </p>
                                            @if ($stage->completed)
                                                <p class="text-[11px] text-gray-400 mt-0.5">
                                                    {{ $stage->completed_at?->translatedFormat('d M Y, H:i') }}
                                                    @if ($stage->completedBy)
                                                        · oleh {{ $stage->completedBy->name }}
                                                    @endif
                                                </p>
                                                @if ($stage->notes)
                                                    <p class="text-[11px] text-gray-500 italic mt-0.5">{{ $stage->notes }}</p>
                                                @endif
                                            @else
                                                <p class="text-[11px] text-gray-300 mt-0.5">Menunggu verifikasi</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Next stage action (admin) --}}
                    @if ($selectedLoan->status !== 'Disetujui' && $selectedLoan->status !== 'Ditolak')
                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan Verifikasi (Opsional)</p>
                            <textarea wire:model="stageNote" rows="2" placeholder="Tambahkan catatan untuk tahapan ini..."
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all resize-none"></textarea>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex gap-2">
                    @if ($selectedLoan->status !== 'Disetujui' && $selectedLoan->status !== 'Ditolak')
                        <button type="button" wire:click="advanceStage({{ $selectedLoan->id }})" wire:loading.attr="disabled"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-1.5">
                            <span wire:loading.remove wire:target="advanceStage" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                Verifikasi Tahap Berikutnya
                            </span>
                            <span wire:loading wire:target="advanceStage">Memproses...</span>
                        </button>
                        <button type="button" wire:click="rejectLoan({{ $selectedLoan->id }})"
                            class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-sm">
                            Tolak
                        </button>
                    @endif
                    <button type="button" wire:click="tutupModal"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Print Script --}}
    <script>
        document.addEventListener('print-disbursement', function (e) {
            const month = e.detail.month;
            const area  = document.getElementById('disbursement-print-area');
            if (!area) return;

            const win = window.open('', '_blank');
            win.document.write(`
                <html><head><title>Export Disbursement - ${month}</title>
                <style>
                    body { font-family: sans-serif; padding: 20px; }
                    h2 { margin-bottom: 4px; } p { color: #666; margin: 0 0 16px; }
                    table { width:100%; border-collapse:collapse; }
                    th { background:#f9fafb; text-align:left; padding:10px 12px; font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:.05em; }
                    td { padding:10px 12px; font-size:13px; border-bottom:1px solid #f3f4f6; }
                </style></head>
                <body>
                    <h2>Laporan Disbursement Operasi</h2>
                    <p>Periode: ${month}</p>
                    ${area.innerHTML}
                </body></html>
            `);
            win.document.close();
            win.print();
        });
    </script>
</div>
