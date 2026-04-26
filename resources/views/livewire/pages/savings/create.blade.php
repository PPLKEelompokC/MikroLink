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
            'amount' => 'required|numeric|min:1000',
            'proof' => 'required|image|max:2048', // Maks 2MB
        ]);

        $path = $this->proof->store('savings-proofs', 'public');

        Savings::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'amount' => $this->amount,
            'proof_path' => $path,
            'status' => 'Pending',
        ]);

        session()->flash('success', 'Setoran berhasil dikirim, menunggu validasi admin.');
        $this->reset(['amount', 'proof']);
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label>Jenis Simpanan</label>
            <select wire:model="type" class="w-full rounded">
                <option value="Pokok">Pokok</option>
                <option value="Wajib">Wajib</option>
                <option value="Sukarela">Sukarela</option>
            </select>
        </div>
        <div>
            <label>Nominal (Rp)</label>
            <input type="number" wire:model="amount" class="w-full rounded">
        </div>
        <div>
            <label>Bukti Transfer</label>
            <input type="file" wire:model="proof" class="w-full">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Kirim Setoran</button>
    </form>
</div>