<?php

use Livewire\Volt\Component;
use App\Models\Savings;

new class extends Component {
    public function approve($id)
    {
        $saving = Savings::findOrFail($id);
        
        // Memanggil fungsi approve() yang ada di model Savings
        // Fungsi ini otomatis menambah saldo_kas koperasi dan mencatat log
        if ($saving->approve()) {
            session()->flash('success', 'Setoran berhasil disetujui.');
        } else {
            session()->flash('error', 'Gagal memproses setoran.');
        }
    }

    public function reject($id)
    {
        $saving = Savings::findOrFail($id);
        $saving->reject('Ditolak oleh admin');
        session()->flash('info', 'Setoran telah ditolak.');
    }

    public function with()
    {
        return [
            'pendingSavings' => Savings::where('status', 'Pending')->with('user')->get(),
        ];
    }
}; ?>

<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Validasi Setoran Simpanan</h2>
    
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pendingSavings as $s)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $s->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $s->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">Rp{{ number_format($s->amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ asset('storage/' . $s->proof_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">Lihat Bukti</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                            <button wire:click="approve({{ $s->id }})" class="bg-green-500 text-white px-3 py-1 rounded text-xs">Approve</button>
                            <button wire:click="reject({{ $s->id }})" class="bg-red-500 text-white px-3 py-1 rounded text-xs">Reject</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada setoran tertunda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>