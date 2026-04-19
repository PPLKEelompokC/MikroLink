<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MikroLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; margin: 0; }
        .bg-fade-wrapper {
            position: absolute; inset: 0; z-index: -1;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 85%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,1) 20%, rgba(0,0,0,0) 85%);
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
            <a href="{{ route('loginPage') }}" class="font-semibold text-gray-800 hover:text-[#e8a838]">Login</a>
            <div class="h-[50px] flex justify-center items-center bg-[#e8a838] px-10 py-2 rounded-[56px] shadow-xl shadow-orange-200/40 cursor-default">
                <span class="font-bold text-[16px] text-white">Register</span>
            </div>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
        <div class="text-center mb-10">
            <div class="mb-6 flex justify-center">
                <div class="border border-[#e8a838]/30 bg-white px-6 py-2 rounded-full flex items-center gap-3 shadow-sm">
                    <img src="{{ asset('images/Logo Mikrolink.png') }}" class="h-6 w-auto">
                    <span class="font-bold text-gray-900 text-[14px]">Anggota MikroLink</span>
                </div>
            </div>
            <h1 class="text-[36px] font-extrabold text-black tracking-tight mb-2">Mulai Langkah Perubahan</h1>
            <p class="text-gray-400 font-medium">Daftar sekarang untuk memiliki akses finansial yang adil dan transparan</p>
        </div>
        <div class="w-full max-w-[480px] bg-white border border-[#e4e4e4] p-10 rounded-[32px] shadow-2xl shadow-gray-100">
            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                    <input type="text" placeholder="Masukkan username" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] transition-all font-medium text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" placeholder="johndoe@gmail.com" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] transition-all font-medium text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" placeholder="••••••••" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] transition-all font-medium text-gray-600">
                </div>
                <button type="submit" class="w-full bg-gradient-to-b from-[#e8a838] to-[#ffa200] text-white font-bold py-4 rounded-2xl shadow-xl shadow-orange-200 flex items-center justify-center gap-3 hover:scale-[1.02] transition-all">
                    <span>Daftar Sekarang</span>
                    <div class="bg-white w-8 h-8 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M7 17L17 7M17 7H7M17 7V17" stroke="url(#blueGradReg)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="blueGradReg" x1="7" y1="7" x2="17" y2="17" gradientUnits="userSpaceOnUse"><stop stop-color="#013599"/><stop offset="1" stop-color="#295fc9"/></linearGradient></defs></svg>
                    </div>
                </button>
            </form>
            <div class="mt-8 text-center"><p class="text-gray-400 font-medium">Sudah punya akun? <a href="{{ route('loginPage') }}" class="text-[#e8a838] font-bold">Login di sini</a></p></div>
        </div>
    </main>
</body>
</html>