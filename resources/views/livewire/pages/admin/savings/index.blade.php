<?php
use Livewire\Volt\Component;
use App\Models\Savings;

new class extends Component {
    public function approve($id)
    {
        $saving = Savings::findOrFail($id);
        if ($saving->approve()) {
            session()->flash('success', 'Setoran disetujui dan saldo kas koperasi bertambah.');
        }
    }

    public function with() {
        return [
            'pendingSavings' => Savings::where('status', 'Pending')->get(),
        ];
    }
}; ?>

<div>
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th>Anggota</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingSavings as $s)
            <tr>
                <td>{{ $s->user->name }}</td>
                <td>{{ $s->type }}</td>
                <td>Rp{{ number_format($s->amount) }}</td>
                <td>
                    <button wire:click="approve({{ $s->id }})" class="text-green-600">Setujui</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>