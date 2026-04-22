<x-layouts.app.header title="Pusat Bantuan">
    <div class="min-h-screen bg-white font-sans" style="font-family: 'Instrument Sans', sans-serif;">

        {{-- TOP NAVBAR --}}
        <nav class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-2">
                {{-- Logo --}}
                <img src="{{ asset('images/mikrolink-logo.png') }}" alt="MikroLink" class="h-12 w-auto"
                onerror="this.style.display='none'; document.getElementById('logo-text').style.display='flex';">
                <div id="logo-text" class="hidden items-center gap-1">
                    <div class="w-8 h-8 rounded-full bg-green-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-green-800 text-lg">MikroLink</span>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
                <a href="#" class="hover:text-gray-900 transition-colors">Dashboard</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Loan Services</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Verifikasi &amp; Keanggotaan</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Report &amp; Analytics</a>
                <a href="#" class="font-semibold text-yellow-500 hover:text-yellow-600 transition-colors">Pusat Bantuan</a>
            </div>
            <div class="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center cursor-pointer">
                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
            </div>
        </nav>

        {{-- HERO SECTION --}}
        <section class="relative overflow-hidden px-6 py-12 bg-white" style="min-height: 220px;">
            {{-- Background diamond pattern --}}
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                @for ($i = 0; $i < 15; $i++)
                    <div class="absolute w-10 h-10 border border-gray-200 rotate-45 opacity-40"
                         style="left: {{ rand(0, 100) }}%; top: {{ rand(0, 100) }}%;"></div>
                @endfor
            </div>
            <div class="relative max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="max-w-lg">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Pusat Bantuan</h1>
                    <p class="text-gray-500 text-base">Temukan jawaban yang Anda butuhkan atau hubungi tim support kami</p>
                </div>
                <div class="flex-shrink-0">
                    {{-- Illustration: person reading --}}
                    <svg width="180" height="150" viewBox="0 0 200 170" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Shadow ellipse -->
                        <ellipse cx="100" cy="155" rx="55" ry="12" fill="#e5e7eb"/>
                        <!-- Floating book platform -->
                        <ellipse cx="100" cy="130" rx="50" ry="14" fill="#1a1a1a"/>
                        <!-- Person body -->
                        <rect x="78" y="70" width="44" height="55" rx="8" fill="#374151"/>
                        <!-- Person head -->
                        <circle cx="100" cy="58" r="18" fill="#fbbf24"/>
                        <!-- Hair -->
                        <path d="M82 52 Q100 35 118 52" fill="#1f2937" stroke="none"/>
                        <!-- Eyes -->
                        <circle cx="93" cy="60" r="2.5" fill="#1f2937"/>
                        <circle cx="107" cy="60" r="2.5" fill="#1f2937"/>
                        <!-- Book in hands -->
                        <rect x="72" y="88" width="56" height="38" rx="4" fill="#fbbf24"/>
                        <rect x="72" y="88" width="28" height="38" rx="4" fill="#f59e0b"/>
                        <line x1="100" y1="90" x2="100" y2="124" stroke="#92400e" stroke-width="1.5"/>
                        <!-- Lines on book -->
                        <line x1="76" y1="98" x2="96" y2="98" stroke="#92400e" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="76" y1="105" x2="96" y2="105" stroke="#92400e" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="76" y1="112" x2="96" y2="112" stroke="#92400e" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="103" y1="98" x2="123" y2="98" stroke="#92400e" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
                        <line x1="103" y1="105" x2="123" y2="105" stroke="#92400e" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
                        <!-- Stars / sparkles -->
                        <text x="140" y="50" fill="#fbbf24" font-size="18" font-weight="bold">✦</text>
                        <text x="155" y="75" fill="#d1d5db" font-size="12">✦</text>
                        <text x="130" y="80" fill="#fbbf24" font-size="10">✦</text>
                        <!-- Small book floating -->
                        <rect x="150" y="90" width="30" height="22" rx="3" fill="#6b7280" transform="rotate(-15 165 101)"/>
                        <rect x="150" y="90" width="15" height="22" rx="3" fill="#4b5563" transform="rotate(-15 165 101)"/>
                    </svg>
                </div>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 pb-20">

            {{-- SEARCH BAR --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 border border-gray-200 rounded-xl px-5 py-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        type="text"
                        placeholder="Cari pertanyaan atau topik bantuan..."
                        class="w-full text-gray-600 placeholder-gray-400 text-sm outline-none border-none bg-transparent"
                    >
                </div>
            </div>

            {{-- CONTACT CHANNELS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                {{-- Call Center --}}
                <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                        <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Call Center</p>
                    <p class="text-gray-500 text-xs mt-1">1500-MIKRO (64576)</p>
                    <p class="text-gray-400 text-xs">Senin – Jumat: 08.00 – 20.00 WIB</p>
                </div>

                {{-- WhatsApp --}}
                <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                        <svg class="w-5 h-5" fill="#f59e0b" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">WhatsApp</p>
                    <p class="text-gray-500 text-xs mt-1">+62 811-2345-6789</p>
                    <p class="text-gray-400 text-xs">24/7 Response</p>
                </div>

                {{-- Email --}}
                <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                        <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Email</p>
                    <p class="text-gray-500 text-xs mt-1">support@mikrolink.id</p>
                    <p class="text-gray-400 text-xs">24/7 Response</p>
                </div>

                {{-- Video Call --}}
                <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                        <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Video Call</p>
                    <p class="text-gray-500 text-xs mt-1">Konsultasi Langsung</p>
                    <p class="text-gray-400 text-xs">Booking via aplikasi</p>
                </div>
            </div>

            {{-- FAQ SECTION --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-6" x-data="{ activeTab: 'semua', openItem: null }">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Frequently Asked Questions (FAQ)</h2>

                {{-- Tabs --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach([['key' => 'semua', 'label' => 'Semua'], ['key' => 'umum', 'label' => 'Umum'], ['key' => 'pinjaman', 'label' => 'Pinjaman'], ['key' => 'pembayaran', 'label' => 'Pembayaran'], ['key' => 'teknis', 'label' => 'Teknis']] as $tab)
                        <button
                            @click="activeTab = '{{ $tab['key'] }}'"
                            :class="activeTab === '{{ $tab['key'] }}' ? 'bg-yellow-400 text-white font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-2 rounded-full text-sm transition-all"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- FAQ Groups --}}
                @php
                $faqGroups = [
                    ['key' => 'umum', 'label' => 'Umum', 'items' => [
                        ['q' => 'Apa itu MikroLink?', 'a' => 'MikroLink adalah platform pinjaman mikro digital yang membantu masyarakat mendapatkan akses keuangan dengan mudah, cepat, dan aman.'],
                        ['q' => 'Bagaimana cara jadi anggota?', 'a' => 'Daftarkan diri Anda melalui aplikasi MikroLink dengan mengisi data diri, upload KTP, dan verifikasi nomor telepon.'],
                        ['q' => 'Apakah data saya aman?', 'a' => 'Ya, data Anda dilindungi dengan enkripsi SSL dan kami mematuhi regulasi perlindungan data pribadi.'],
                    ]],
                    ['key' => 'pinjaman', 'label' => 'Pinjaman', 'items' => [
                        ['q' => 'Berapa lama proses persetujuan pinjaman?', 'a' => 'Proses persetujuan pinjaman biasanya membutuhkan waktu 1–3 hari kerja setelah dokumen lengkap.'],
                        ['q' => 'Apa saja syarat pengajuan pinjaman?', 'a' => 'Syarat pengajuan meliputi: KTP, slip gaji atau bukti penghasilan, dan rekening bank aktif.'],
                        ['q' => 'Bagaimana cara membatalkan pinjaman?', 'a' => 'Pembatalan pinjaman dapat dilakukan sebelum dana dicairkan melalui menu pengajuan di aplikasi.'],
                        ['q' => 'Bisakah saya melunasi pinjaman lebih awal?', 'a' => 'Ya, Anda dapat melunasi pinjaman lebih awal tanpa biaya penalti tambahan.'],
                    ]],
                    ['key' => 'pembayaran', 'label' => 'Pembayaran', 'items' => [
                        ['q' => 'Apa saja metode pembayaran yang tersedia?', 'a' => 'Pembayaran dapat dilakukan melalui transfer bank, virtual account, dompet digital, dan minimarket.'],
                        ['q' => 'Bagaimana jika saya terlambat bayar?', 'a' => 'Keterlambatan pembayaran akan dikenakan denda sesuai ketentuan. Segera hubungi tim kami jika mengalami kendala.'],
                        ['q' => 'Apakah saya bisa mengubah tanggal jatuh tempo?', 'a' => 'Perubahan tanggal jatuh tempo dapat diajukan melalui layanan pelanggan kami.'],
                    ]],
                    ['key' => 'teknis', 'label' => 'Teknis', 'items' => [
                        ['q' => 'Aplikasi tidak bisa login, apa yang harus dilakukan?', 'a' => 'Pastikan koneksi internet Anda stabil, hapus cache aplikasi, atau coba reset password Anda.'],
                        ['q' => 'Bagaimana cara mengupdate data profil?', 'a' => 'Buka menu Profil di aplikasi, pilih Edit, lakukan perubahan dan simpan.'],
                        ['q' => 'Notifikasi tidak masuk, bagaimana solusinya?', 'a' => 'Pastikan izin notifikasi sudah diaktifkan di pengaturan ponsel Anda untuk aplikasi MikroLink.'],
                    ]],
                ];
                @endphp

                <div class="space-y-6">
                    @foreach($faqGroups as $group)
                        <div
                            x-show="activeTab === 'semua' || activeTab === '{{ $group['key'] }}'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                        >
                            <h3 class="text-base font-bold text-gray-900 mb-3">{{ $group['label'] }}</h3>
                            <div class="space-y-2">
                                @foreach($group['items'] as $idx => $item)
                                    @php $itemId = $group['key'] . '-' . $idx; @endphp
                                    <div
                                        class="border border-gray-100 rounded-xl overflow-hidden"
                                        x-data="{ open: false }"
                                    >
                                        <button
                                            @click="open = !open"
                                            class="w-full flex items-center justify-between px-4 py-3.5 text-left text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                        >
                                            <span class="flex items-center gap-2">
                                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                {{ $item['q'] }}
                                            </span>
                                        </button>
                                        <div
                                            x-show="open"
                                            x-collapse
                                            class="px-4 pb-4 text-sm text-gray-500 leading-relaxed border-t border-gray-50"
                                        >
                                            <p class="pt-3">{{ $item['a'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SUMBER DAYA LAINNYA --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Sumber Daya Lainnya</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Panduan --}}
                    <div class="border border-gray-100 rounded-xl p-4 bg-white hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                            <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm">Panduan Penggunaan Platform</p>
                        <p class="text-gray-400 text-xs mt-1">Tutorial lengkap menggunakan semua fitur MikroLink</p>
                    </div>
                    {{-- Video --}}
                    <div class="border border-gray-100 rounded-xl p-4 bg-white hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                            <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm">Video Tutorial</p>
                        <p class="text-gray-400 text-xs mt-1">Belajar melalui video step-by-step</p>
                    </div>
                    {{-- Kebijakan --}}
                    <div class="border border-gray-100 rounded-xl p-4 bg-white hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                            <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm">Kebijakan Privasi</p>
                        <p class="text-gray-400 text-xs mt-1">Informasi tentang perlindungan data Anda</p>
                    </div>
                    {{-- Syarat --}}
                    <div class="border border-gray-100 rounded-xl p-4 bg-white hover:shadow-md transition-all cursor-pointer hover:-translate-y-0.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background-color: #fef3c7;">
                            <svg class="w-5 h-5" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm">Syarat &amp; Ketentuan</p>
                        <p class="text-gray-400 text-xs mt-1">Aturan dan ketentuan penggunaan layanan</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- CTA FOOTER --}}
        <section class="py-16 text-center" style="background: linear-gradient(180deg, #fef9ee 0%, #fde68a 100%);">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Tidak Menemukan Jawaban?</h2>
            <p class="text-gray-600 text-sm mb-7 max-w-md mx-auto">
                Tim support kami siap membantu Anda. Buat tiket support dan kami akan merespon dalam waktu 1–3 hari bisnis.
            </p>
            <a href="{{ route('ticketing.index') }}" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-8 py-3.5 rounded-full transition-colors shadow-md hover:shadow-lg text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Buat Tiket
            </a>
        </section>

    </div>
</x-layouts.app.header>