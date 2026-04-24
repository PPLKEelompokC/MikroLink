<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MikroLink')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- WAJIB: Tambahkan script Livewire & Flux agar komponen muncul --}}
    @livewireStyles
    @fluxStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #ffffff; margin: 0; overflow-x: hidden; }

        #loader {
            position: fixed; inset: 0; background: white; z-index: 9999;
            display: flex; justify-content: center; align-items: center;
            transition: opacity 0.6s cubic-bezier(0.77, 0, 0.175, 1), visibility 0.6s;
        }
        .logo-3d {
            width: 100px;
            animation: flip3d 1.2s ease-in-out infinite;
            filter: drop-shadow(0 15px 30px rgba(232, 168, 56, 0.2));
        }
        @keyframes flip3d {
            0% { transform: perspective(400px) rotateY(0deg); }
            100% { transform: perspective(400px) rotateY(360deg); }
        }
        .loader-hidden { opacity: 0; visibility: hidden; }

        /* DIAMOND PATTERN GLOBAL */
        .bg-global-diamond {
            position: fixed; inset: 0; z-index: -1;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='160' viewBox='0 0 300 160'%3E%3Cpath d='M150 0 L300 80 L150 160 L0 80 Z' fill='%23e4e7ec' fill-opacity='0.4' /%3E%3C/svg%3E");
            background-size: 320px 170px;
            -webkit-mask-image: linear-gradient(to bottom, white 10%, transparent 80%);
            mask-image: linear-gradient(to bottom, white 10%, transparent 80%);
        }

        /* Margin untuk konten agar tidak tertutup sidebar di desktop */
        @media (min-width: 1024px) {
            .has-sidebar { margin-left: 16rem; }
        }
    </style>
</head>
<body>
    {{-- Loader 3D MikroLink --}}
    <div id="loader">
        <img src="{{ asset('images/logo-mikrolink.png') }}" class="logo-3d">
    </div>

    <div class="bg-global-diamond"></div>

    {{-- 1. Tampilkan Sidebar hanya jika user sudah Login --}}
    @auth
        @include('components.layouts.app.sidebar')
    @endauth

    {{-- 2. Kontainer Utama --}}
    <main class="{{ auth()->check() ? 'has-sidebar' : '' }}">
        {{-- Untuk Blade biasa (Dashboard Lama/Login/Register) --}}
        @yield('content')

        {{-- Untuk Livewire Volt (Fitur Ajukan Pinjaman/Setor) --}}
        @isset($slot)
            {{ $slot }}
        @endisset
    </main>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- WAJIB: Tambahkan script Livewire & Flux di bawah --}}
    @livewireScripts
    @fluxScripts

    <script>
        window.addEventListener("load", () => {
            const loader = document.getElementById("loader");
            setTimeout(() => { loader.classList.add("loader-hidden"); }, 700);
        });
    </script>

    @stack('scripts')
</body>
</html>