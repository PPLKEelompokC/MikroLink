@extends('layouts.app')

@section('title', 'Siklus Inklusi - MikroLink')

@section('content')
    <nav class="w-full h-[100px] flex justify-between items-center px-12 bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-gray-50">
        <a href="{{ route('landingPage') }}">
            <img src="{{ asset('images/Logo Mikrolink.png') }}" class="w-[130px] h-auto">
        </a>
        <div class="flex gap-10 items-center">
            <a href="{{ route('loginPage') }}" class="font-bold text-gray-800 hover:text-[#e8a838] transition-colors">Login</a>
            <a href="{{ route('registerPage') }}" class="bg-[#e8a838] text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-orange-100 hover:bg-[#ffa200] transition-all">Register</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-12 py-24">
        <div class="mb-32">
            <h1 class="text-7xl font-[800] tracking-tighter leading-none mb-6">Siklus Inklusi<br><span class="text-gray-300">Modern.</span></h1>
            <p class="text-xl text-gray-500 max-w-lg font-medium">Langkah transparan kami mengelola amanah Koperasi Merah Putih untuk pemberdayaan bangsa.</p>
        </div>

        <div class="space-y-40">
            <div class="flex flex-col md:flex-row gap-20 items-start">
                <div class="text-[120px] font-black leading-none text-orange-100 italic">01</div>
                <div class="max-w-xl">
                    <h3 class="text-4xl font-extrabold mb-6">Identitas Digital</h3>
                    <p class="text-gray-500 text-lg leading-relaxed">
                        Keamanan data anggota adalah prioritas. Dengan <b>KYC Digital</b>, setiap identitas diverifikasi secara sah untuk menjamin validitas komunitas kita.
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row-reverse gap-20 items-start">
                <div class="text-[120px] font-black leading-none text-blue-100 italic">02</div>
                <div class="max-w-xl text-right md:text-left">
                    <h3 class="text-4xl font-extrabold mb-6 text-[#013599]">Portal Aspirasi</h3>
                    <p class="text-gray-500 text-lg leading-relaxed">
                        Setiap suara warga berharga. Ajukan dukungan modal produktif atau kebutuhan keluarga langsung melalui antarmuka inklusif kami.
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-20 items-start">
                <div class="text-[120px] font-black leading-none text-orange-100 italic">03</div>
                <div class="max-w-xl">
                    <h3 class="text-4xl font-extrabold mb-6">Indeks Kepercayaan</h3>
                    <p class="text-gray-500 text-lg leading-relaxed font-bold italic text-black">"Rekam jejak, bukan sekadar skor kredit."</p>
                    <p class="text-gray-500 text-lg leading-relaxed mt-4">
                        Kami menilai kelayakan berdasarkan integritas dan partisipasi aktif Anda dalam menjaga amanah dana bersama secara adil.
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection