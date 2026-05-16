<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\CapitalLog;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public function with(): array
    {
        $logs = CapitalLog::with('user')
            ->latest()
            ->paginate(15);

        return ['logs' => $logs];
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
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Riwayat Perubahan Modal</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Daftar lengkap seluruh transaksi dan perubahan modal koperasi.</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Waktu</th>
                            <th class="px-8 py-4">Tipe Transaksi</th>
                            <th class="px-8 py-4">Pelaku / Anggota</th>
                            <th class="px-8 py-4">Aliran Kas</th>
                            <th class="px-8 py-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-sm text-gray-600 font-medium">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $log->type_label }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-tr from-gray-100 to-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-xs font-bold uppercase">
                                            {{ substr($log->member_name ?? ($log->user ? $log->user->name : 'SYS'), 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $log->member_name ?? ($log->user ? $log->user->name : 'System') }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $log->transaction_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($log->transaction_type === 'deposit')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase tracking-widest">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m0-16l-4 4m4-4l4 4"/></svg>
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-500 uppercase tracking-widest">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 20V4m0 16l-4-4m4 4l4-4"/></svg>
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right font-extrabold text-sm {{ $log->transaction_type === 'deposit' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $log->transaction_type === 'deposit' ? '+' : '-' }} Rp {{ number_format($log->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center text-gray-400 text-sm italic">
                                    Belum ada log perubahan modal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-8 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
