<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Aspirasi - MikroLink</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f9fafb; margin: 0; }

        .bg-fade-wrapper {
            position: absolute; inset: 0; z-index: -1;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 50%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 50%);
        }

        .diamond-pattern {
            width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='160' viewBox='0 0 300 160'%3E%3Cpath d='M150 0 L300 80 L150 160 L0 80 Z' fill='%23e4e7ec' fill-opacity='0.4' /%3E%3C/svg%3E");
            background-size: 320px 170px; background-position: center top;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col relative overflow-x-hidden">

    <div class="bg-fade-wrapper"><div class="diamond-pattern"></div></div>

    <nav class="w-full h-[80px] flex justify-between items-center bg-white/90 backdrop-blur-sm px-12 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" alt="Logo" class="w-[110px] h-auto object-contain">
            </a>
            <div class="h-6 w-[1px] bg-gray-200"></div>
            <span class="font-bold text-gray-800 text-sm tracking-tight uppercase">Portal Aspirasi Warga</span>
        </div>
        <div x-data="{ open: false }" class="relative flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ Auth::check() ? (Auth::user()->role ?? 'Pejuang Ekonomi') : 'Pejuang Ekonomi' }}</p>
                <p class="text-sm font-extrabold text-gray-800">{{ Auth::user()->name ?? 'Guest' }}</p>
            </div>
            
            <button @click="open = !open" @click.away="open = false" class="focus:outline-none flex items-center gap-2">
                <div class="w-12 h-12 bg-gradient-to-tr from-[#e8a838] to-[#ffa200] rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold hover:scale-105 transition-transform">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'GU' }}
                </div>
            </button>

            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50" style="display: none;">
                @if(Auth::check())
                    <div class="px-4 py-2 border-b border-gray-50 sm:hidden">
                        <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#e8a838] transition-colors">Edit Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#e8a838] transition-colors">Login</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl mx-auto w-full px-12 py-12">
        
        <section class="mb-12">
            <h1 class="text-[32px] font-extrabold text-black tracking-tight mb-3">Suarakan Aspirasi, <span class="text-[#e8a838]">Wujudkan Mandiri.</span></h1>
            <p class="text-gray-500 max-w-2xl leading-relaxed">Pintu digital bagi setiap warga untuk mengajukan dukungan produktif demi kesejahteraan keluarga dan penghapusan kemiskinan (SDG 1).</p>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            
            <div class="group bg-white border border-gray-100 p-8 rounded-[32px] shadow-sm hover:shadow-2xl hover:shadow-orange-100 transition-all cursor-pointer border-b-4 border-b-[#ffa200]">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-[#e8a838]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h3 class="text-xl font-extrabold text-black mb-3">Dukungan Modal Produktif</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-8">Ajukan bantuan modal untuk usaha mikro, alat produksi, atau pengembangan skill ekonomi mandiri.</p>
                <button onclick="fillAndScroll('Dukungan Modal Produktif')" class="flex items-center gap-3 text-[#e8a838] font-bold text-sm uppercase tracking-wider group-hover:gap-5 transition-all">
                    Ajukan Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

            <div class="group bg-white border border-gray-100 p-8 rounded-[32px] shadow-sm hover:shadow-2xl hover:shadow-blue-50 transition-all cursor-pointer border-b-4 border-b-[#013599]">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-[#013599]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <h3 class="text-xl font-extrabold text-black mb-3">Kesejahteraan Keluarga</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-8">Dukungan untuk kebutuhan dasar pendidikan, kesehatan, dan gizi keluarga pra-sejahtera.</p>
                <button onclick="fillAndScroll('Kesejahteraan Keluarga')" class="flex items-center gap-3 text-[#013599] font-bold text-sm uppercase tracking-wider group-hover:gap-5 transition-all">
                    Ajukan Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

        </div>

        <section id="form-section" class="bg-white border border-gray-100 rounded-[32px] overflow-hidden shadow-sm mb-16 p-8">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Form Pengajuan Aspirasi</h2>
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl font-bold border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('aspiration.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="text-left">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul (Subject)</label>
                    <input id="subject" name="subject" required type="text" placeholder="Misal: Bantuan Modal Usaha Sembako" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] focus:ring-4 focus:ring-orange-50 transition-all font-medium text-gray-600">
                </div>

                <div class="text-left">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pesan Aspirasi</label>
                    <textarea name="message" required rows="4" placeholder="Ceritakan detail kebutuhan Anda di sini..."
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] focus:ring-4 focus:ring-orange-50 transition-all font-medium text-gray-600 resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#ffa200] text-white font-bold py-4 rounded-2xl shadow-xl shadow-orange-200 hover:bg-[#e8a838] transition-all flex items-center justify-center gap-2">
                    Kirim Aspirasi Sekarang
                </button>
            </form>
        </section>

        <section class="bg-white border border-gray-100 rounded-[32px] overflow-hidden shadow-sm">
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Status Aspirasi Saya</h3>
                <span class="text-xs font-bold text-[#e8a838] bg-orange-50 px-3 py-1 rounded-full uppercase">3 Aspirasi Aktif</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Tgl Pengajuan</th>
                            <th class="px-8 py-4">Kategori Aspirasi</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-5 text-sm font-medium text-gray-600">19 April 2026</td>
                            <td class="px-8 py-5 text-sm font-bold text-gray-900">Modal Usaha Warung Sembako</td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[11px] font-bold rounded-full uppercase">Ditinjau</span>
                            </td>
                            <td class="px-8 py-5 text-right font-bold text-[#013599] text-sm cursor-pointer hover:underline">Detail</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-5 text-sm font-medium text-gray-600">15 April 2026</td>
                            <td class="px-8 py-5 text-sm font-bold text-gray-900">Beasiswa Anak Sekolah Dasar</td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded-full uppercase">Disetujui</span>
                            </td>
                            <td class="px-8 py-5 text-right font-bold text-[#013599] text-sm cursor-pointer hover:underline">Detail</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <footer class="w-full py-8 text-center text-gray-400 text-xs font-medium border-t border-gray-100">
        MikroLink Smart Inclusion &copy; 2026 — Mendukung Percepatan Target SDG 1 (Tanpa Kemiskinan)
    </footer>

    <script>
        function fillAndScroll(subjectText) {
            document.getElementById('subject').value = subjectText;
            document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>