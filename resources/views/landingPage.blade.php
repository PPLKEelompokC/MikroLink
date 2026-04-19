<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroLink - Transformasi Ekonomi Kolektif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; margin: 0; }
        .bg-fade-wrapper {
            position: absolute; inset: 0; z-index: -1;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 75%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 75%);
        }
        .diamond-pattern {
            width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='160' viewBox='0 0 300 160'%3E%3Cpath d='M150 0 L300 80 L150 160 L0 80 Z' fill='%23e4e7ec' fill-opacity='0.6' /%3E%3C/svg%3E");
            background-size: 320px 170px; background-position: center top;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col overflow-x-hidden relative">
    <div class="bg-fade-wrapper"><div class="diamond-pattern"></div></div>

    <nav class="w-full h-[100px] flex justify-between items-center bg-white/90 backdrop-blur-sm px-12 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('landingPage') }}"><img src="{{ asset('images/Logo Mikrolink.png') }}" class="w-[130px] h-auto"></a>
        </div>
        <div class="flex items-center gap-10">
            <a href="{{ route('loginPage') }}" class="font-semibold text-[16px] text-gray-800 hover:text-[#e8a838]">Login</a>
            <a href="{{ route('registerPage') }}" class="h-[50px] flex justify-center items-center bg-[#e8a838] px-10 py-2 rounded-[56px] shadow-xl shadow-orange-200/40 hover:bg-[#ffa200] transition-all">
                <span class="font-bold text-[16px] text-white">Register</span>
            </a>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-6 -mt-20">
        <div class="mb-14 border border-[#e8a838]/30 bg-white px-8 py-3 rounded-full flex items-center gap-4 shadow-sm">
            <img src="{{ asset('images/Logo Mikrolink.png') }}" class="h-10 w-auto">
            <span class="font-bold text-gray-900 text-[18px]">MikroLink</span>
        </div>
        <h1 class="max-w-5xl font-bold text-[50px] leading-[68px] text-black tracking-tight mb-8">
            Berdaya Bersama, <span class="text-[#9ca3af]">Membangun Kemandirian</span> Ekonomi Bangsa Melalui Sistem Terintegrasi
        </h1>
        <p class="max-w-3xl text-gray-500 text-[17px] leading-[1.8] mb-14 px-10">
            Bergabung bersama ribuan pejuang ekonomi — 1000+ anggota telah mewujudkan akses finansial yang adil dan transparan melalui ekosistem kolektif MikroLink.
        </p>
        <div class="flex items-center gap-5">
            <a href="{{ route('registerPage') }}" class="flex justify-center items-center gap-5 bg-gradient-to-b from-[#e8a838] to-[#ffa200] pl-10 pr-3 py-3 rounded-full shadow-2xl shadow-orange-300/60 hover:scale-105 transition-all">
                <span class="font-bold text-[17px] text-white">Gabung Sekarang</span>
                <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M7 17L17 7M17 7H7M17 7V17" stroke="url(#blueGrad)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="blueGrad" x1="7" y1="7" x2="17" y2="17" gradientUnits="userSpaceOnUse"><stop stop-color="#013599"/><stop offset="1" stop-color="#295fc9"/></linearGradient></defs></svg>
                </div>
            </a>
            
            <a href="{{ route('caraKerja') }}" class="flex justify-center items-center px-12 py-5 bg-white rounded-full border border-[#d5d7da] font-bold text-[17px] text-[#414651] shadow-sm hover:bg-gray-50 transition-colors cursor-pointer">
                <span>Lihat Cara Kerja</span>
            </a>
        </div>
    </main>
</body>
</html>