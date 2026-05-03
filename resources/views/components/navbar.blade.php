<nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
    <div class="flex items-center">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
        </a>
    </div>
    <div class="hidden lg:flex items-center gap-8">
        <a href="{{ route('dashboard') }}" class="font-bold text-[15px] text-[#e8a838]">Dashboard</a>
        @if(auth()->check() && in_array(auth()->user()->role, ['Admin Koperasi', 'Manajer Koperasi', 'admin', 'super_admin']))
            <a href="{{ route('koperasi.edit') }}" class="font-bold text-[15px] text-emerald-600 hover:text-emerald-700 transition-colors">Manage Koperasi</a>
            <a href="{{ route('admin.simpanan.validasi') }}"
                class="font-bold text-[15px] text-orange-600 hover:text-orange-700 transition-colors flex items-center gap-1.5">
                Validasi Setoran
                @if(isset($pendingDepositsCount) && $pendingDepositsCount > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-orange-500 text-white text-[10px] font-extrabold rounded-full">
                        {{ $pendingDepositsCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.fund-allocation.index') }}" class="font-bold text-[15px] text-violet-600 hover:text-violet-700 transition-colors flex items-center gap-1.5">
                AI Fund Allocation
                @if(isset($pendingAllocationsCount) && $pendingAllocationsCount > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-violet-500 text-white text-[10px] font-extrabold rounded-full">{{ $pendingAllocationsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pinjaman.validasi') }}" class="font-bold text-[15px] text-indigo-600 hover:text-indigo-700 transition-colors">Validasi Pinjaman</a>
            <a href="{{ route('dashboard') }}#aspiration-management" class="font-bold text-[15px] text-blue-600 hover:text-blue-700 transition-colors">Aspirations Portal</a>
            <a href="{{ route('dashboard') }}#trust-management" class="font-bold text-[15px] text-blue-600 hover:text-blue-700 transition-colors">Trust Index</a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.audit-trails') }}" class="font-bold text-[15px] text-red-600 hover:text-red-700 transition-colors">Audit Trail</a>
            @endif
        @endif
    </div>
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner hover:bg-gray-400 transition-colors">
                @if(auth()->check())
                    {{ auth()->user()->initials() }}
                @else
                    GU
                @endif
            </div>
        </button>
        
        <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50" style="display: none;">
            @if(auth()->check())
                <div class="px-4 py-2 border-b border-gray-50">
                    <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
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
