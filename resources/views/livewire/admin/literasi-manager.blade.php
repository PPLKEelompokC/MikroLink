<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\LiterasiArtikel;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $showModal = false;
    public $isEditing = false;
    public $articleId = null;

    public $judul = '';
    public $ringkasan = '';
    public $konten = '';
    public $kategori = 'menabung';
    public $estimasi_baca = 5;
    public $level = 'pemula';
    public $is_published = false;

    public function rules()
    {
        return [
            'judul' => 'required|string|max:255',
            'ringkasan' => 'required|string|max:500',
            'konten' => 'required|string',
            'kategori' => 'required|string',
            'estimasi_baca' => 'required|integer|min:1',
            'level' => 'required|in:pemula,menengah,mahir',
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['judul', 'ringkasan', 'konten', 'kategori', 'estimasi_baca', 'level', 'is_published', 'articleId']);
        $this->isEditing = false;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->articleId = $id;

        $article = LiterasiArtikel::findOrFail($id);
        $this->judul = $article->judul;
        $this->ringkasan = $article->ringkasan;
        $this->konten = $article->konten;
        $this->kategori = $article->kategori;
        $this->estimasi_baca = $article->estimasi_baca;
        $this->level = $article->level;
        $this->is_published = $article->is_published;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $article = LiterasiArtikel::findOrFail($this->articleId);
            $article->update([
                'judul' => $this->judul,
                'slug' => Str::slug($this->judul),
                'ringkasan' => $this->ringkasan,
                'konten' => $this->konten,
                'kategori' => $this->kategori,
                'estimasi_baca' => $this->estimasi_baca,
                'level' => $this->level,
            ]);
        } else {
            LiterasiArtikel::create([
                'judul' => $this->judul,
                'slug' => Str::slug($this->judul),
                'ringkasan' => $this->ringkasan,
                'konten' => $this->konten,
                'kategori' => $this->kategori,
                'estimasi_baca' => $this->estimasi_baca,
                'level' => $this->level,
                'is_published' => false,
                'created_by' => auth()->id(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('article-saved');
    }

    public function togglePublish($id)
    {
        $article = LiterasiArtikel::findOrFail($id);
        $article->update(['is_published' => !$article->is_published]);
    }

    public function delete($id)
    {
        LiterasiArtikel::findOrFail($id)->delete();
    }

    public function with()
    {
        return [
            'artikels' => LiterasiArtikel::with('penulis')->latest()->paginate(10),
            'kategoriOptions' => LiterasiArtikel::daftarKategori()
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Manajemen Ruang Tumbuh</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola artikel literasi finansial untuk para anggota koperasi.</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tulis Artikel Baru
        </button>
    </div>

    <!-- Tabel Daftar Artikel -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4">Judul Artikel</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($artikels as $artikel)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $artikel->judul }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $artikel->ringkasan }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $kategoriData = $kategoriOptions[$artikel->kategori] ?? null;
                                @endphp
                                @if($kategoriData)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-{{ $kategoriData['warna'] }}-50 text-{{ $kategoriData['warna'] }}-600">
                                        <span>{{ $kategoriData['icon'] }}</span>
                                        {{ $kategoriData['label'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 capitalize">{{ $artikel->kategori }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="togglePublish({{ $artikel->id }})" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition-colors {{ $artikel->is_published ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <div class="w-2 h-2 rounded-full {{ $artikel->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></div>
                                    {{ $artikel->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-600">{{ number_format($artikel->views) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="openEditModal({{ $artikel->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button wire:click="delete({{ $artikel->id }})" wire:confirm="Hapus artikel ini?" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada artikel. Klik "Tulis Artikel Baru" untuk mulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $artikels->links() }}
        </div>
    </div>

    <!-- Modal Form Create / Edit -->
    @if($showModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 10)">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300"
             x-show="show"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             wire:click="$set('showModal', false)"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl shadow-2xl transition-all duration-300 transform"
             x-show="show"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="sticky top-0 bg-white/80 backdrop-blur-md px-6 py-4 border-b border-gray-100 flex items-center justify-between z-10">
                <h3 class="text-lg font-bold text-gray-900">{{ $isEditing ? 'Edit Artikel' : 'Tulis Artikel Baru' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-6">
                <!-- Judul -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Artikel</label>
                    <input type="text" wire:model="judul" placeholder="Contoh: Pentingnya Menabung Sejak Dini"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                    @error('judul') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori, Level, Estimasi Baca -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori</label>
                        <select wire:model="kategori" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            @foreach($kategoriOptions as $key => $kat)
                                <option value="{{ $key }}">{{ $kat['label'] }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Level</label>
                        <select wire:model="level" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <option value="pemula">Pemula</option>
                            <option value="menengah">Menengah</option>
                            <option value="mahir">Mahir</option>
                        </select>
                        @error('level') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Estimasi Baca (Menit)</label>
                        <input type="number" min="1" wire:model="estimasi_baca" 
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                        @error('estimasi_baca') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Ringkasan -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Ringkasan Singkat (Muncul di Daftar Artikel)</label>
                    <textarea wire:model="ringkasan" rows="2" placeholder="Tuliskan ringkasan singkat atau pengantar artikel..."
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all"></textarea>
                    @error('ringkasan') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Konten Utama -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Konten Artikel</label>
                    <textarea wire:model="konten" rows="12" placeholder="Tuliskan isi artikel Anda di sini..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all leading-relaxed"></textarea>
                    @error('konten') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
