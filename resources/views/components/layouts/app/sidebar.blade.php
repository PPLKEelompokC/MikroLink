<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        {{-- Pastikan Styles di-load agar tidak ada tampilan kosong --}}
        @livewireStyles
        @fluxStyles
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="Platform" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Layanan Koperasi" class="grid">
                    {{-- Ikon currency-dollar jauh lebih stabil --}}
                    <flux:navlist.item icon="currency-dollar" :href="route('simpanan.setor')" :current="request()->routeIs('simpanan.setor')" wire:navigate>
                        Setor Simpanan
                    </flux:navlist.item>
                    
                    <flux:navlist.item icon="credit-card" :href="route('pinjaman.ajukan')" :current="request()->routeIs('pinjaman.ajukan')" wire:navigate>
                        Ajukan Pinjaman
                    </flux:navlist.item>
                </flux:navlist.group>

                {{-- Proteksi Role untuk Admin --}}
                @if(auth()->user()?->role === 'Admin Koperasi' || auth()->user()?->role === 'Manajer Koperasi')
                    <flux:navlist.group heading="Administrasi" class="grid">
                        <flux:navlist.item icon="check-circle" :href="route('admin.simpanan.validasi')" :current="request()->routeIs('admin.simpanan.validasi')" wire:navigate>
                            Validasi Simpanan
                        </flux:navlist.item>
                        
                        <flux:navlist.item icon="pencil-square" :href="route('koperasi.edit')" :current="request()->routeIs('koperasi.edit')" wire:navigate>
                            Profil Koperasi
                        </flux:navlist.item>
                        
                        <flux:navlist.item icon="document-text" :href="route('admin.docs.index')" :current="request()->routeIs('admin.docs.index')" wire:navigate>
                            Validasi Dokumen
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    Repository
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                    Documentation
                </flux:navlist.item>
            </flux:navlist>

            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()?->name ?? 'Guest'"
                    :initials="auth()->user()?->initials() ?? 'GU'"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()?->initials() ?? 'GU' }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'Guest' }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()?->email ?? 'guest@example.com' }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full text-left">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()?->initials() ?? 'GU'" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item href="/settings/profile" icon="cog">Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full text-left">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- AREA KONTEN: Menampilkan Form Volt atau Blade Biasa --}}
        <main class="p-6 lg:p-10">
            @isset($slot)
                {{ $slot }}
            @endisset

            @yield('content')
        </main>

        @livewireScripts
        @fluxScripts
    </body>
</html>