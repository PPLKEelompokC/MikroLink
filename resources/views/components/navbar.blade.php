<nav class="w-full h-[68px] flex justify-between items-center bg-white/90 backdrop-blur-md px-8 border-b border-[#e4e4e4] sticky top-0 z-50 shadow-sm">
    {{-- Logo --}}
    <div class="flex items-center">
        <a href="{{ route('dashboard') }}" wire:navigate>
            <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[110px] h-auto">
        </a>
    </div>

    {{-- Nav Links (Desktop) --}}
    <div class="hidden lg:flex items-center gap-1">
        {{-- Dashboard — visible to all authenticated users --}}
        <a href="{{ route('dashboard') }}" wire:navigate
            class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
            Dashboard
        </a>

        @auth
            @php $role = auth()->user()->role; @endphp

            {{-- Admin + Manager + Super Admin links --}}
            @if (in_array($role, ['admin', 'manager', 'super_admin', 'Admin Koperasi', 'Manajer Koperasi', 'Super Admin']))

                <a href="{{ route('koperasi.edit') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Koperasi
                </a>

                <a href="{{ route('admin.simpanan.validasi') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all flex items-center gap-1.5">
                    Validasi Setoran
                    @if(isset($pendingDepositsCount) && $pendingDepositsCount > 0)
                        <span class="inline-flex items-center justify-center w-4 h-4 bg-[#e8a838] text-white text-[9px] font-extrabold rounded-full">
                            {{ $pendingDepositsCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.simpanan.tarik.validasi') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Validasi Penarikan
                </a>

                <a href="{{ route('admin.pinjaman.review') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Review Pinjaman
                </a>

                <a href="{{ route('admin.pinjaman.validasi') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Tracking Pinjaman
                </a>

                <a href="{{ route('admin.neraca.index') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold {{ request()->routeIs('admin.neraca.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }} transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Neraca
                </a>

            @endif

            {{-- Manager + Super Admin only --}}
            @if (in_array($role, ['manager', 'super_admin', 'Manajer Koperasi', 'Super Admin']))

                <a href="{{ route('admin.pinjaman.review.manajer') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Review Manajer
                </a>

                <a href="{{ route('admin.fund-allocation.index') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all flex items-center gap-1.5">
                    AI Fund Allocation
                    @if(isset($pendingAllocationsCount) && $pendingAllocationsCount > 0)
                        <span class="inline-flex items-center justify-center w-4 h-4 bg-[#e8a838] text-white text-[9px] font-extrabold rounded-full">{{ $pendingAllocationsCount }}</span>
                    @endif
                </a>

            @endif

            {{-- Super Admin only --}}
            @if (in_array($role, ['super_admin', 'Super Admin']))

                <a href="{{ route('admin.audit-trails') }}" wire:navigate
                    class="px-3 py-1.5 rounded-lg text-[13.5px] font-semibold text-gray-500 hover:text-[#e8a838] hover:bg-amber-50 transition-all">
                    Audit Trail
                </a>

            @endif
        @endauth
    </div>

    {{-- User Avatar Dropdown --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.away="open = false"
            class="flex items-center gap-2 focus:outline-none group">
            <div class="w-9 h-9 bg-[#e8a838] rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm group-hover:bg-[#d4952f] transition-colors">
                @auth
                    {{ auth()->user()->initials() }}
                @else
                    GU
                @endauth
            </div>
        </button>

        <div x-show="open" x-transition.opacity.duration.200ms
            class="absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-2xl shadow-xl py-1.5 z-50"
            style="display: none;">
            @auth
                <div class="px-4 py-2.5 border-b border-gray-50">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider text-[#e8a838] bg-amber-50 px-2 py-0.5 rounded-md">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <a href="{{ route('settings.profile') }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Pengaturan Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Login</a>
            @endauth
        </div>
    </div>
</nav>