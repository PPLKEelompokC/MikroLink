<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Savings;

new class extends Component {
    use WithFileUploads;

    public $type = 'Sukarela';
    public $amount;
    public $proof;

    public function save()
    {
        $this->validate([
            'type' => 'required|in:Pokok,Wajib,Sukarela',
            'amount' => 'required|numeric|min:10000',
            'proof' => 'required|image|max:2048', // Maksimal 2MB
        ]);

        // Simpan file bukti transfer ke folder storage/app/public/savings-proofs
        $path = $this->proof->store('savings-proofs', 'public');

        Savings::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'amount' => $this->amount,
            'proof_path' => $path,
            'status' => 'Pending',
        ]);

        session()->flash('success', 'Setoran berhasil dikirim! Silakan tunggu validasi admin.');
        
        $this->reset(['amount', 'proof']);
    }
}; ?>

<div class="p-6 bg-white rounded-lg shadow">
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Jenis Simpanan</label>
            <select wire:model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="Pokok">Simpanan Pokok</option>
                <option value="Wajib">Simpanan Wajib</option>
                <option value="Sukarela">Simpanan Sukarela</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nominal Setoran (Rp)</label>
            <input type="number" wire:model="amount" placeholder="Contoh: 50000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Unggah Bukti Transfer</label>
            <input type="file" wire:model="proof" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <div wire:loading wire:target="proof" class="text-sm text-gray-500 mt-1">Mengunggah...</div>
        </div>

        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Kirim Setoran
        </button>
    </form>
</div>