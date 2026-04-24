<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Deposit;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {

    public ?Deposit $selectedDeposit = null;
    public string $adminNote = '';
    public string $filterStatus = 'PENDING';

    public function with(): array
    {
        $deposits = Deposit::with('user')
            ->when($this->filterStatus !== 'ALL', fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        return ['deposits' => $deposits];
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedDeposit = Deposit::with('user')->findOrFail($id);
        $this->adminNote = $this->selectedDeposit->admin_note ?? '';
    }

    public function tutupModal(): void
    {
        $this->selectedDeposit = null;
        $this->adminNote = '';
    }

    public function approve(int $id): void
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->update([
            'status'     => 'APPROVED',
            'admin_note' => $this->adminNote ?: 'Setoran telah diverifikasi dan disetujui.',
        ]);
        $this->tutupModal();
        $this->dispatch('notif', type: 'success', message: 'Setoran berhasil disetujui!');
    }

    public function reject(int $id): void
    {
        $this->validate([
            'adminNote' => 'required|min:5',
        ], [
            'adminNote.required' => 'Catatan wajib diisi saat menolak setoran.',
            'adminNote.min'      => 'Catatan minimal 5 karakter.',
        ]);

        $deposit = Deposit::findOrFail($id);
        $deposit->update([
            'status'     => 'REJECTED',
            'admin_note' => $this->adminNote,
        ]);
        $this->tutupModal();
        $this->dispatch('notif', type: 'error', message: 'Setoran telah ditolak.');
    }
};
?>

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

    {{-- Konten Halaman --}}
    <div class="w-full max-w-6xl mx-auto py-10 px-6">

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

        {{-- Header --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-8 bg-gray-50/30 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Validasi Setoran Simpanan</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Periksa dan validasi bukti setoran yang dikirim anggota.</p>
                    </div>
                </div>
            </div>

            {{-- Filter Tab --}}
            <div class="px-8 py-4 flex gap-2">
                @foreach (['PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak', 'ALL' => 'Semua'] as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('filterStatus', '{{ $value }}')"
                        class="px-4 py-1.5 rounded-full text-xs font-bold transition-all
                            {{ $filterStatus === $value
                                ? 'bg-gray-900 text-white'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Tabel Setoran --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Anggota</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($deposits as $deposit)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center text-white text-xs font-bold uppercase flex-shrink-0">
                                            {{ substr($deposit->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $deposit->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $deposit->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $jenisColor = [
                                            'POKOK'    => 'bg-indigo-50 text-indigo-600',
                                            'WAJIB'    => 'bg-blue-50 text-blue-600',
                                            'SUKARELA' => 'bg-emerald-50 text-emerald-600',
                                        ][$deposit->type] ?? 'bg-gray-50 text-gray-600';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold {{ $jenisColor }} uppercase tracking-widest">
                                        {{ $deposit->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-extrabold text-gray-900">
                                        Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-500">{{ $deposit->created_at->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $deposit->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusColor = [
                                            'PENDING'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'APPROVED' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'REJECTED' => 'bg-red-50 text-red-600 border-red-100',
                                        ][$deposit->status] ?? 'bg-gray-50 text-gray-600';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold border {{ $statusColor }} uppercase tracking-widest">
                                        {{ $deposit->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        wire:click="lihatDetail({{ $deposit->id }})"
                                        class="px-4 py-1.5 bg-gray-900 hover:bg-gray-700 text-white text-xs font-bold rounded-xl transition-colors">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-400 italic">Tidak ada data setoran.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail Setoran --}}
    @if ($selectedDeposit)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                wire:click="tutupModal"></div>

            {{-- Modal Card --}}
            <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-md overflow-hidden z-10"
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm">Detail Setoran</h3>
                    </div>
                    <button wire:click="tutupModal"
                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 space-y-3 max-h-[65vh] overflow-y-auto">

                    {{-- Info Anggota + Status --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center text-white text-xs font-bold uppercase flex-shrink-0">
                                {{ substr($selectedDeposit->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm leading-tight">{{ $selectedDeposit->user->name }}</p>
                                <p class="text-[11px] text-gray-400">{{ $selectedDeposit->user->email }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-widest
                            {{ $selectedDeposit->status === 'APPROVED' ? 'bg-emerald-50 text-emerald-600' :
                               ($selectedDeposit->status === 'REJECTED' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                            {{ $selectedDeposit->status }}
                        </span>
                    </div>

                    {{-- Detail Grid 3 Kolom --}}
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-3 bg-gray-50 rounded-xl text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nominal</p>
                            <p class="text-xs font-extrabold text-gray-900">Rp {{ number_format($selectedDeposit->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis</p>
                            <p class="text-xs font-extrabold text-gray-900">{{ $selectedDeposit->type }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal</p>
                            <p class="text-[11px] font-bold text-gray-900">{{ $selectedDeposit->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400">{{ $selectedDeposit->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Bukti Transfer --}}
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bukti Transfer</p>
                        <div class="rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
                            <img src="{{ Storage::url($selectedDeposit->proof_path) }}"
                                alt="Bukti Transfer"
                                class="w-full max-h-48 object-contain cursor-pointer"
                                onclick="window.open(this.src, '_blank')">
                        </div>
                        <p class="text-[10px] text-gray-400 text-center mt-1">Klik gambar untuk memperbesar</p>
                    </div>

                    {{-- Catatan Admin --}}
                    @if ($selectedDeposit->status === 'PENDING')
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                                Catatan <span class="text-red-400">(wajib jika ditolak)</span>
                            </label>
                            <textarea
                                wire:model="adminNote"
                                rows="2"
                                placeholder="Tuliskan catatan untuk anggota..."
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all resize-none"
                            ></textarea>
                            @error('adminNote')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        @if ($selectedDeposit->admin_note)
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Catatan Admin</p>
                                <p class="text-sm text-gray-700">{{ $selectedDeposit->admin_note }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Modal Footer --}}
                @if ($selectedDeposit->status === 'PENDING')
                    <div class="px-5 py-4 border-t border-gray-100 flex gap-2">
                        <button
                            type="button"
                            wire:click="approve({{ $selectedDeposit->id }})"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-1.5">
                            <span wire:loading.remove wire:target="approve" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui
                            </span>
                            <span wire:loading wire:target="approve" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Proses...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="reject({{ $selectedDeposit->id }})"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-1.5">
                            <span wire:loading.remove wire:target="reject" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak
                            </span>
                            <span wire:loading wire:target="reject" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Proses...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="tutupModal"
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-colors text-sm">
                            Batal
                        </button>
                    </div>
                @else
                    <div class="px-5 py-4 border-t border-gray-100">
                        <button
                            type="button"
                            wire:click="tutupModal"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition-colors text-sm">
                            Tutup
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>