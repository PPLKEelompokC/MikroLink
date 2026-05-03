<x-layouts.app.header title="Buat Tiket Baru">
<div class="min-h-screen bg-gray-50" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink" class="h-10 w-auto"
                onerror="this.style.display='none'; document.getElementById('nav-logo-fb2').style.display='flex';">
            <div id="nav-logo-fb2" class="hidden items-center gap-1">
                <div class="w-8 h-8 rounded-full bg-green-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                </div>
                <span class="font-bold text-green-800 text-lg">MikroLink</span>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition-colors">Dashboard</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Loan Services</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Verifikasi &amp; Keanggotaan</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Report &amp; Analytics</a>
            <a href="{{ route('pusat-bantuan') }}" class="font-semibold text-yellow-500 hover:text-yellow-600 transition-colors">Pusat Bantuan</a>
        </div>
        <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center cursor-pointer text-sm font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </nav>

    {{-- HERO --}}
    <div class="bg-white border-b border-gray-100 px-6 py-10 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            @for ($i = 0; $i < 8; $i++)
                <div class="absolute w-8 h-8 border border-gray-200 rotate-45 opacity-30"
                     style="left:{{ ($i * 13) % 100 }}%; top:{{ ($i * 19) % 100 }}%;"></div>
            @endfor
        </div>
        <div class="relative max-w-3xl mx-auto">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-3">
                <a href="{{ route('pusat-bantuan') }}" class="hover:text-yellow-500 transition-colors">Pusat Bantuan</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('ticketing.index') }}" class="hover:text-yellow-500 transition-colors">Tiket Saya</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium">Buat Tiket</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Tiket Baru</h1>
            <p class="text-gray-500 mt-1 text-sm">Isi formulir di bawah dan tim kami akan segera membantu Anda</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-6">

            {{-- FORM --}}
            <div class="md:col-span-2">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900">Detail Tiket</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Semua field bertanda * wajib diisi</p>
                    </div>

                    <form action="{{ route('ticketing.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                        @csrf

                        {{-- Subjek --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Subjek Tiket <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="Contoh: Tidak bisa login ke aplikasi"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all @error('subject') border-red-400 bg-red-50 @enderror"
                            >
                            @error('subject')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Kategori & Prioritas --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select name="category"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all bg-white @error('category') border-red-400 bg-red-50 @enderror">
                                    <option value="">Pilih kategori</option>
                                    <option value="umum"       {{ old('category') == 'umum'       ? 'selected' : '' }}>Umum</option>
                                    <option value="pinjaman"   {{ old('category') == 'pinjaman'   ? 'selected' : '' }}>Pinjaman</option>
                                    <option value="pembayaran" {{ old('category') == 'pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                                    <option value="teknis"     {{ old('category') == 'teknis'     ? 'selected' : '' }}>Teknis</option>
                                    <option value="lainnya"    {{ old('category') == 'lainnya'    ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Prioritas <span class="text-red-500">*</span>
                                </label>
                                <select name="priority"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all bg-white @error('priority') border-red-400 bg-red-50 @enderror">
                                    <option value="">Pilih prioritas</option>
                                    <option value="low"    {{ old('priority') == 'low'    ? 'selected' : '' }}>🟢 Rendah</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Sedang</option>
                                    <option value="high"   {{ old('priority') == 'high'   ? 'selected' : '' }}>🔴 Tinggi</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Deskripsi Masalah <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="description"
                                rows="6"
                                placeholder="Jelaskan masalah Anda secara detail. Sertakan langkah-langkah yang sudah dicoba, pesan error yang muncul, dll."
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all resize-none @error('description') border-red-400 bg-red-50 @enderror"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Attachment --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Lampiran <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-yellow-400 transition-colors cursor-pointer"
                                 onclick="document.getElementById('attachment').click()">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <p class="text-sm text-gray-500">Klik untuk upload atau drag & drop</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF — Maks. 2MB</p>
                                <input type="file" id="attachment" name="attachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="document.getElementById('file-name').textContent = this.files[0]?.name || ''">
                            </div>
                            <p id="file-name" class="mt-1.5 text-xs text-yellow-600 font-medium"></p>
                            @error('attachment')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-colors shadow-sm">
                                Kirim Tiket
                            </button>
                            <a href="{{ route('ticketing.index') }}"
                               class="px-6 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl text-sm hover:bg-gray-50 transition-colors text-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SIDEBAR INFO --}}
            <div class="space-y-4">
                {{-- Tips --}}
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Tips Membuat Tiket
                    </h3>
                    <ul class="space-y-2.5 text-xs text-gray-500">
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 flex-shrink-0 mt-1.5"></span>
                            Jelaskan masalah secara spesifik dan detail
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 flex-shrink-0 mt-1.5"></span>
                            Sertakan screenshot jika ada pesan error
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 flex-shrink-0 mt-1.5"></span>
                            Pilih kategori yang sesuai untuk respon lebih cepat
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 flex-shrink-0 mt-1.5"></span>
                            Satu tiket untuk satu masalah
                        </li>
                    </ul>
                </div>

                {{-- Response Time --}}
                <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-5">
                    <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu Respon
                    </h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-1.5 text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span> Prioritas Tinggi
                            </span>
                            <span class="font-semibold text-gray-800">2-4 jam</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-1.5 text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-yellow-400"></span> Prioritas Sedang
                            </span>
                            <span class="font-semibold text-gray-800">1 hari kerja</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-1.5 text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span> Prioritas Rendah
                            </span>
                            <span class="font-semibold text-gray-800">1-3 hari kerja</span>
                        </div>
                    </div>
                </div>

                {{-- Contact Alternatives --}}
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 text-sm mb-3">Butuh Bantuan Cepat?</h3>
                    <div class="space-y-2">
                        <a href="https://wa.me/6281123456789" target="_blank"
                           class="flex items-center gap-3 text-xs text-gray-600 hover:text-green-600 transition-colors py-1.5">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp: +62 811-2345-6789
                        </a>
                        <a href="tel:150064578"
                           class="flex items-center gap-3 text-xs text-gray-600 hover:text-yellow-600 transition-colors py-1.5">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Call Center: 1500-MIKRO
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app.header>