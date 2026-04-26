@extends('layouts.app')

@section('title', 'Daftar Akun - MikroLink')

@section('content')
    <div class="absolute inset-0 z-[-1] opacity-30 pointer-events-none">
        <div class="w-full h-full" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'160\' viewBox=\'0 0 300 160\'%3E%3Cpath d=\'M150 0 L300 80 L150 160 L0 80 Z\' fill=\'%23e4e7ec\' /%3E%3C/svg%3E'); background-size: 320px 170px;"></div>
    </div>

    <nav class="w-full h-[100px] flex justify-between items-center bg-white/90 backdrop-blur-sm px-12 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" class="w-[130px] h-auto">
            </a>
        </div>
        <div class="flex items-center gap-10">
            <a href="{{ route('login') }}" class="font-semibold text-gray-800 hover:text-[#e8a838] transition-colors">Login</a>
            <div class="h-[50px] flex justify-center items-center bg-[#e8a838] px-10 py-2 rounded-[56px] shadow-xl shadow-orange-200/40 cursor-default">
                <span class="font-bold text-[16px] text-white">Register</span>
            </div>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12 relative z-10">
        
        <div class="text-center mb-10">
            <div class="mb-6 flex justify-center">
                <div class="border border-[#e8a838]/30 bg-white px-6 py-2 rounded-full flex items-center gap-3 shadow-sm">
                    <img src="{{ asset('images/logo-mikrolink.png') }}" class="h-6 w-auto">
                    <span class="font-bold text-gray-900 text-[14px]">Anggota MikroLink</span>
                </div>
            </div>
            <h1 class="text-[36px] font-extrabold text-black tracking-tight mb-2">Mulai Langkah Perubahan</h1>
            <p class="text-gray-400 font-medium">Daftar sekarang untuk memiliki akses finansial yang adil dan transparan.</p>
        </div>

        <div class="w-full max-w-[480px] bg-white border border-[#e4e4e4] p-10 rounded-[32px] shadow-2xl shadow-gray-100">
            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="text-left">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan username unik" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] focus:ring-4 focus:ring-orange-50 transition-all font-medium text-gray-600">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-left">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="johndoe@gmail.com" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] focus:ring-4 focus:ring-orange-50 transition-all font-medium text-gray-600">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-left">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi</label>
                    <input type="password" name="password" placeholder="••••••••" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:border-[#e8a838] focus:ring-4 focus:ring-orange-50 transition-all font-medium text-gray-600">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-b from-[#e8a838] to-[#ffa200] text-white font-bold py-5 rounded-2xl shadow-xl shadow-orange-200 flex items-center justify-center gap-3 hover:scale-[1.02] active:scale-[0.98] transition-all group">
                    <span class="text-[17px]">Daftar Akun Sekarang</span>
                    <div class="bg-white w-9 h-9 rounded-full flex items-center justify-center shadow-sm group-hover:rotate-12 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="url(#blueGradReg)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="blueGradReg" x1="7" y1="7" x2="17" y2="17" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#013599"/><stop offset="1" stop-color="#295fc9"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-400 font-medium">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-[#013599] font-bold hover:underline">Login di sini</a>
                </p>
            </div>
        </div>
    </main>
@endsection