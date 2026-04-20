@extends('layouts.app')

@section('title', 'Login - MikroLink')

@section('content')
    <div class="min-h-screen flex overflow-hidden relative z-10">
        <main class="w-1/2 flex flex-col justify-center px-[12%] py-12 bg-white">
            <div class="mb-10">
                <h1 class="text-[48px] font-extrabold text-black tracking-tighter mb-2">Selamat Datang Kembali</h1>
                <p class="text-gray-400 font-medium text-[16px]">Masuk untuk terus bertumbuh dan memantau kontribusi nyatamu dalam ekosistem kolektif kami!</p>
            </div>
            
            <div class="w-full max-w-[420px]">
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="johndoe@gmail.com" class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl outline-none focus:border-[#e8a838] transition-all font-medium text-gray-600">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" placeholder="masukkan password" class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl outline-none focus:border-[#e8a838] transition-all font-medium text-gray-600">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 text-right"><a href="#" class="text-[14px] font-bold text-[#013599] hover:underline">Lupa password?</a></div>
                    </div>
                    <button type="submit" class="w-full bg-[#ffa200] text-white font-bold py-4 rounded-full shadow-xl shadow-orange-200 hover:bg-[#e8a838] transition-all">Masuk</button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-gray-500 font-medium text-[15px]">Belum punya akun? <a href="{{ route('register') }}" class="text-[#013599] font-bold hover:underline">Daftar</a></p>
                </div>

                <div class="relative my-10 flex items-center">
                    <div class="flex-grow border-t border-gray-100"></div>
                    <span class="mx-4 text-gray-400 text-sm font-medium">Atau</span>
                    <div class="flex-grow border-t border-gray-100"></div>
                </div>

                <button class="w-full flex items-center justify-center gap-3 border border-gray-200 py-4 rounded-full hover:bg-gray-50 font-bold text-gray-700 transition-colors">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5">
                    <span>Masuk dengan Google</span>
                </button>
            </div>
        </main>

        <div class="w-1/2 p-8">
            <div class="w-full h-full bg-[#ffa200] rounded-[48px] overflow-hidden flex items-center justify-center shadow-inner relative">
                <div class="absolute inset-0 opacity-60" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'110\' viewBox=\'0 0 200 110\'%3E%3Cpath d=\'M100 0 L200 55 L100 110 L0 55 Z\' fill=\'%23ffffff\' /%3E%3C/svg%3E'); background-size: 140px 80px;"></div>
                
                <img src="{{ asset('images/logo-mikrolink.png') }}" class="w-48 h-auto relative z-10 brightness-0 invert opacity-90">
            </div>
        </div>
    </div>
@endsection