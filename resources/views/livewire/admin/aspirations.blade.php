<?php

use Livewire\Volt\Component;
use App\Models\Aspiration;

new class extends Component {
    public function delete($id)
    {
        Aspiration::findOrFail($id)->delete();
        session()->flash('message', 'Aspirasi berhasil dihapus.');
    }

    public function updateStatus($id, $status)
    {
        $aspiration = Aspiration::findOrFail($id);
        $aspiration->update(['status' => $status]);
        session()->flash('message', 'Status aspirasi diperbarui menjadi ' . $status . '.');
    }

    public function with()
    {
        return [
            'aspirations' => Aspiration::latest()->get(),
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900">Aspiration Management Portal</h2>
            <p class="text-xs text-gray-400 font-medium">Kelola dan tinjau semua aspirasi dari anggota koperasi.</p>
        </div>
        <div class="bg-blue-50 px-4 py-2 rounded-xl">
            <span class="text-blue-600 text-xs font-bold uppercase tracking-widest">Admin Control Panel</span>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm font-bold rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="overflow-hidden border border-gray-100 rounded-3xl shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50">
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Tgl Pengajuan</th>
                    <th class="px-6 py-4">Subjek</th>
                    <th class="px-6 py-4">Pesan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($aspirations as $aspiration)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $aspiration->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-900">{{ $aspiration->subject }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-600 line-clamp-1 max-w-xs">{{ $aspiration->message }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                @php
                                    $statusMap = [
                                        'pending' => ['label' => 'Ditinjau', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                        'resolved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-50 text-red-600 border-red-100'],
                                    ];
                                    $currentStatus = $statusMap[strtolower($aspiration->status)] ?? ['label' => $aspiration->status, 'class' => 'bg-gray-50 text-gray-600 border-gray-100'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-extrabold border uppercase tracking-widest {{ $currentStatus['class'] }}">
                                    {{ $currentStatus['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-2">
                                @if($aspiration->status === 'pending')
                                    <button wire:click="updateStatus({{ $aspiration->id }}, 'resolved')" class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors" title="Setujui">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    <button wire:click="updateStatus({{ $aspiration->id }}, 'rejected')" class="p-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors" title="Tolak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                @endif
                                <button wire:click="delete({{ $aspiration->id }})" wire:confirm="Apakah Anda yakin ingin menghapus aspirasi ini?" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic text-xs">Belum ada aspirasi yang masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
