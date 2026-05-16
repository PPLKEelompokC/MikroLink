<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siklus Inklusi - MikroLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; margin: 0; overflow-x: hidden; }
        
        .bg-fade-wrapper {
            position: fixed; inset: 0; z-index: -1;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 75%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 75%);
        }
        .diamond-pattern {
            width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='160' viewBox='0 0 300 160'%3E%3Cpath d='M150 0 L300 80 L150 160 L0 80 Z' fill='%23e4e7ec' fill-opacity='0.4' /%3E%3C/svg%3E");
            background-size: 320px 170px; background-position: center top;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(228, 231, 236, 0.5);
        }

        .step-number {
            font-size: 140px;
            background: linear-gradient(180deg, #f3f4f6 0%, transparent 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="min-h-screen relative antialiased">
    <div class="bg-fade-wrapper"><div class="diamond-pattern"></div></div>

    <nav class="w-full h-[80px] glass-nav flex justify-between items-center px-8 md:px-24 sticky top-0 z-50">
        <a href="{{ route('home') }}" class="transition-transform hover:scale-105 active:scale-95">
            <img src="{{ asset('images/logo-mikrolink.png') }}" class="w-[110px] h-auto" alt="MikroLink Logo">
        </a>
        <div class="flex gap-6 items-center">
            <a href="{{ route('login') }}" class="text-[14px] font-bold text-gray-700 hover:text-[#e8a838] transition-colors">Login</a>
            <a href="{{ route('register') }}" class="bg-[#e8a838] text-white px-7 py-2.5 rounded-full text-[14px] font-bold shadow-xl shadow-orange-100 hover:bg-[#ffa200] hover:-translate-y-0.5 transition-all active:scale-95">
                Register
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 md:px-24 py-20">
        <header class="mb-32 reveal" id="hero">
            <h1 class="text-6xl md:text-8xl font-[900] tracking-tight leading-[0.9] mb-8 text-gray-900">
                Siklus Inklusi<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-300 to-gray-100">Modern.</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-500 max-w-xl font-medium leading-relaxed">
                Langkah transparan kami mengelola amanah Koperasi Merah Putih untuk pemberdayaan bangsa secara berkelanjutan.
            </p>
        </header>

        <div class="space-y-64 pb-32">
            <!-- Step 01 -->
            <section class="flex flex-col md:flex-row gap-12 md:gap-24 items-center reveal">
                <div class="relative flex-shrink-0">
                    <span class="step-number font-black italic">01</span>
                    <div class="absolute inset-0 flex items-center justify-center translate-y-8">
                        <div class="w-40 h-40 bg-orange-50 rounded-full blur-3xl opacity-60"></div>
                    </div>
                </div>
                <div class="max-w-2xl">
                    <div class="inline-block px-4 py-1.5 bg-orange-50 text-[#e8a838] text-xs font-black uppercase tracking-widest rounded-lg mb-6">
                        Tahap Verifikasi
                    </div>
                    <h3 class="text-4xl md:text-5xl font-black mb-6 text-gray-900 tracking-tight">Identitas Digital</h3>
                    <p class="text-gray-500 text-lg md:text-xl leading-relaxed">
                        Keamanan data anggota adalah prioritas utama. Dengan sistem <span class="text-gray-900 font-bold">KYC Digital</span> yang terenkripsi, setiap identitas diverifikasi secara sah untuk menjamin validitas dan kepercayaan dalam komunitas kita.
                    </p>
                </div>
            </section>

            <!-- Step 02 -->
            <section class="flex flex-col md:flex-row-reverse gap-12 md:gap-24 items-center reveal">
                <div class="relative flex-shrink-0">
                    <span class="step-number font-black italic text-blue-50/50">02</span>
                    <div class="absolute inset-0 flex items-center justify-center translate-y-8">
                        <div class="w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-60"></div>
                    </div>
                </div>
                <div class="max-w-2xl text-left md:text-right">
                    <div class="inline-block px-4 py-1.5 bg-blue-50 text-[#013599] text-xs font-black uppercase tracking-widest rounded-lg mb-6">
                        Tahap Partisipasi
                    </div>
                    <h3 class="text-4xl md:text-5xl font-black mb-6 text-[#013599] tracking-tight">Portal Aspirasi</h3>
                    <p class="text-gray-500 text-lg md:text-xl leading-relaxed">
                        Setiap suara warga berharga bagi ekosistem. Ajukan dukungan modal produktif atau kebutuhan keluarga langsung melalui <span class="text-[#013599] font-bold">Antarmuka Inklusif</span> kami yang dirancang untuk kemudahan akses bagi semua kalangan.
                    </p>
                </div>
            </section>

            <!-- Step 03 -->
            <section class="flex flex-col md:flex-row gap-12 md:gap-24 items-center reveal">
                <div class="relative flex-shrink-0">
                    <span class="step-number font-black italic">03</span>
                    <div class="absolute inset-0 flex items-center justify-center translate-y-8">
                        <div class="w-40 h-40 bg-amber-50 rounded-full blur-3xl opacity-60"></div>
                    </div>
                </div>
                <div class="max-w-2xl">
                    <div class="inline-block px-4 py-1.5 bg-amber-50 text-[#e8a838] text-xs font-black uppercase tracking-widest rounded-lg mb-6">
                        Tahap Evaluasi
                    </div>
                    <h3 class="text-4xl md:text-5xl font-black mb-6 text-gray-900 tracking-tight">Indeks Kepercayaan</h3>
                    <div class="relative pl-8 border-l-4 border-[#e8a838] mb-6">
                        <p class="text-gray-900 text-xl md:text-2xl font-bold italic leading-tight">
                            "Rekam jejak, bukan sekadar skor kredit."
                        </p>
                    </div>
                    <p class="text-gray-500 text-lg md:text-xl leading-relaxed">
                        Kami menilai kelayakan berdasarkan <span class="text-gray-900 font-bold">integritas dan partisipasi aktif</span> Anda dalam menjaga amanah dana bersama. Sistem evaluasi kami memastikan distribusi modal dilakukan secara adil dan tepat sasaran.
                    </p>
                </div>
            </section>
        </div>

        <footer class="mt-32 pt-20 border-t border-gray-100 text-center reveal">
            <h4 class="text-2xl font-bold text-gray-900 mb-8">Siap Memulai Perjalanan Inklusi Anda?</h4>
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="w-full md:w-auto bg-[#e8a838] text-white px-12 py-4 rounded-full font-bold shadow-2xl shadow-orange-200 hover:bg-[#ffa200] hover:-translate-y-1 transition-all">
                    Daftar Sekarang
                </a>
                <a href="{{ route('home') }}" class="w-full md:w-auto bg-white text-gray-700 border border-gray-200 px-12 py-4 rounded-full font-bold hover:bg-gray-50 transition-all">
                    Kembali ke Beranda
                </a>
            </div>
        </footer>
    </main>

    <script>
        // Simple scroll reveal
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        // Initial check
        document.addEventListener("DOMContentLoaded", reveal);
    </script>
</body>
</html>