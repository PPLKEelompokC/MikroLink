<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\AuditTrail;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public ?AuditTrail $selectedTrail = null;
    public string $filterAction = '';

    public function with(): array
    {
        $trails = AuditTrail::with('user')
            ->when($this->filterAction !== '', fn ($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(15);

        return ['trails' => $trails];
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedTrail = AuditTrail::with('user')->findOrFail($id);
    }

    public function tutupModal(): void
    {
        $this->selectedTrail = null;
    }
}; ?>

<div>

    <div class="w-full max-w-7xl mx-auto py-10 px-6">
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

    {{-- Card Container --}}
    <div class="bg-white rounded-[32px] border border-neutral-200 shadow-sm overflow-hidden mb-6">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-[16px] font-bold text-gray-900">System Security Audit Trail</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pantau semua perubahan dan aktivitas sistem</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Status Filter --}}
                    <select wire:model.live="filterAction"
                        class="text-xs font-semibold border border-gray-200 rounded-xl px-3 py-2 bg-white text-gray-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">Semua Aksi</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Model</th>
                        <th class="px-6 py-4">ID Model</th>
                        <th class="px-6 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($trails as $trail)
                        @php
                            $actionConfig = [
                                'created' => 'bg-emerald-100 text-emerald-700',
                                'updated' => 'bg-amber-100 text-amber-700',
                                'deleted' => 'bg-red-100 text-red-700',
                            ][$trail->action] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $trail->created_at->format('d M Y, H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($trail->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 uppercase">
                                        {{ substr($trail->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-700 font-medium">{{ $trail->user->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $trail->user->role }}</span>
                                    </div>
                                </div>
                                @else
                                <span class="text-sm text-gray-500 italic">System / Unauthenticated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase {{ $actionConfig }}">
                                    {{ $trail->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 font-mono text-[11px]">{{ class_basename($trail->model_type) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">#{{ $trail->model_id }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="lihatDetail({{ $trail->id }})"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-xs font-bold transition-colors">
                                    Lihat
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm italic">
                                Belum ada log aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($trails->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $trails->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </div>

    {{-- Modal Detail --}}
    @if ($selectedTrail)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="modal-{{ $selectedTrail->id }}">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="tutupModal"></div>

            <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-2xl overflow-hidden z-10"
                x-data x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Detail Audit Trail</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">Log ID: #{{ $selectedTrail->id }}</p>
                    </div>
                    <button wire:click="tutupModal" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 max-h-[75vh] overflow-y-auto space-y-5">
                    {{-- Info Summary --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pengguna</p>
                            <p class="text-sm font-bold text-gray-900">{{ $selectedTrail->user ? $selectedTrail->user->name : 'System' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">IP Address</p>
                            <p class="text-sm font-medium text-gray-700">{{ $selectedTrail->ip_address ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">User Agent</p>
                            <p class="text-xs font-medium text-gray-500 break-words">{{ $selectedTrail->user_agent ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Data Changes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($selectedTrail->old_values)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nilai Lama (Old Values)</p>
                            <pre class="bg-gray-50 p-4 rounded-xl text-[11px] font-mono text-gray-700 overflow-x-auto border border-gray-100">{{ json_encode($selectedTrail->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                        
                        @if($selectedTrail->new_values)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nilai Baru (New Values)</p>
                            <pre class="bg-emerald-50/50 p-4 rounded-xl text-[11px] font-mono text-gray-700 overflow-x-auto border border-emerald-100">{{ json_encode($selectedTrail->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" wire:click="tutupModal"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
