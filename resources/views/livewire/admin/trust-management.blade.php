<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\TrustMetric;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $users;
    public $selectedUserId;
    public $participation = 50;
    public $integrity = 50;
    public $reliability = 50;
    public $notes = '';
    public $showModal = false;

    public function mount()
    {
        $this->users = User::where('role', 'user')->get();
    }

    public function editScore($userId)
    {
        $this->selectedUserId = $userId;
        $metric = TrustMetric::where('user_id', $userId)->first();
        
        if ($metric) {
            $this->participation = $metric->participation_score;
            $this->integrity = $metric->integrity_score;
            $this->reliability = $metric->reliability_score;
            $this->notes = $metric->notes;
        } else {
            $this->participation = 50;
            $this->integrity = 50;
            $this->reliability = 50;
            $this->notes = '';
        }

        $this->showModal = true;
    }

    public function updateTrustScore()
    {
        $this->validate([
            'participation' => 'required|numeric|min:0|max:100',
            'integrity' => 'required|numeric|min:0|max:100',
            'reliability' => 'required|numeric|min:0|max:100',
        ]);

        TrustMetric::updateOrCreate(
            ['user_id' => $this->selectedUserId],
            [
                'participation_score' => $this->participation,
                'integrity_score' => $this->integrity,
                'reliability_score' => $this->reliability,
                'notes' => $this->notes,
            ]
        );

        $this->showModal = false;
        $this->dispatch('notify', 'Trust score updated successfully!');
    }
}; ?>

<div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-12">
    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-orange-50/30">
        <h3 class="font-bold text-gray-800">Manajemen Indeks Kepercayaan Anggota</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50">
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <th class="px-8 py-4">Anggota</th>
                    <th class="px-8 py-4">Index Terakhir</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    @php 
                        $metric = $user->trustMetric; 
                        $score = $metric ? $metric->final_index : 50;
                        $color = $score >= 70 ? 'text-emerald-600' : ($score >= 40 ? 'text-amber-600' : 'text-red-600');
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-[#e8a838] font-bold text-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black {{ $color }}">{{ number_format($score, 1) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($score >= 70)
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-full border border-emerald-100">HIGH TRUST</span>
                            @elseif($score >= 40)
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-bold rounded-full border border-amber-100">MODERATE</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-bold rounded-full border border-red-100">LOW TRUST</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                            <button wire:click="editScore({{ $user->id }})" class="text-xs font-bold text-[#e8a838] hover:underline">Update Score</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white w-full max-w-md rounded-[32px] shadow-2xl p-8 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-xl font-bold text-gray-900">Update Trust Score</h4>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="updateTrustScore" class="space-y-6">
                    <div>
                        <label class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            <span>Partisipasi (40%)</span>
                            <span class="text-[#e8a838]">{{ $participation }}</span>
                        </label>
                        <input type="range" wire:model.live="participation" min="0" max="100" class="w-full h-2 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-[#e8a838]">
                    </div>
                    <div>
                        <label class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            <span>Integritas (40%)</span>
                            <span class="text-[#e8a838]">{{ $integrity }}</span>
                        </label>
                        <input type="range" wire:model.live="integrity" min="0" max="100" class="w-full h-2 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-[#e8a838]">
                    </div>
                    <div>
                        <label class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            <span>Konsistensi (20%)</span>
                            <span class="text-[#e8a838]">{{ $reliability }}</span>
                        </label>
                        <input type="range" wire:model.live="reliability" min="0" max="100" class="w-full h-2 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-[#e8a838]">
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Catatan Admin</label>
                        <textarea wire:model="notes" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:border-orange-300 transition-all" rows="3" placeholder="Alasan perubahan score..."></textarea>
                    </div>

                    <div class="bg-orange-50 p-4 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#e8a838]">Estimasi Final Index:</span>
                            <span class="text-lg font-black text-[#e8a838]">
                                {{ number_format(($participation * 0.4) + ($integrity * 0.4) + ($reliability * 0.2), 1) }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#e8a838] text-white font-bold rounded-2xl shadow-lg shadow-orange-100 hover:bg-[#d4952f] transition-all">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
