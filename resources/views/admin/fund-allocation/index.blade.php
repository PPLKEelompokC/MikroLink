@extends('layouts.app')

@section('title', 'AI Fund Allocation - MikroLink')

@section('content')
    <nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
            </a>
        </div>
        <div class="hidden lg:flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="font-bold text-[15px] text-gray-600 hover:text-[#e8a838] transition-colors">Dashboard</a>
            <a href="{{ route('admin.fund-allocation.index') }}" class="font-bold text-[15px] text-[#e8a838]">AI Fund Allocation</a>
        </div>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner hover:bg-gray-400 transition-colors">
                    {{ auth()->user()->initials() }}
                </div>
            </button>
            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50" style="display: none;">
                <div class="px-4 py-2 border-b border-gray-50">
                    <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="w-full max-w-[1400px] mx-auto px-10 py-12 flex flex-col gap-8 relative z-10">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-[32px] font-bold text-gray-900 tracking-tight">AI Strategic Fund Allocation</h1>
                <p class="text-gray-500 mt-1">Rekomendasi alokasi dana idle dari AI berdasarkan analisis finansial koperasi.</p>
            </div>
            @if(auth()->user()->role === 'Manajer Koperasi')
                <div x-data="{ loading: false }">
                    <form method="POST" action="{{ route('admin.fund-allocation.analyze') }}" @submit="loading = true">
                        @csrf
                        <button type="submit" :disabled="loading" class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white font-bold px-6 py-3 rounded-2xl transition-all duration-200 shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                            <template x-if="!loading">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </template>
                            <template x-if="loading">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Menyiapkan Analisis...' : 'Jalankan Analisis AI'"></span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Status Filters --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            @php
                $currentStatus = request('status');
                $statuses = [
                    null => 'Semua',
                    'pending' => 'Pending',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak'
                ];
            @endphp
            @foreach($statuses as $value => $label)
                <a href="{{ route('admin.fund-allocation.index', ['status' => $value]) }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-200 border 
                   {{ $currentStatus == $value ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-100' : 'bg-white text-gray-500 border-gray-100 hover:border-gray-300' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Allocations Table --}}
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">Rekomendasi Alokasi Dana</h3>
                </div>
                <span class="text-xs bg-violet-50 text-violet-600 font-bold px-3 py-1.5 rounded-full">{{ $allocations->total() }} rekomendasi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Tanggal</th>
                            <th class="px-8 py-4">Kategori</th>
                            <th class="px-8 py-4">Jumlah Rekomendasi</th>
                            <th class="px-8 py-4 text-center">Confidence</th>
                            <th class="px-8 py-4 text-center">Status</th>
                            <th class="px-8 py-4">AI Model</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($allocations as $allocation)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-sm text-gray-700 font-medium">{{ $allocation->created_at->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $allocation->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-gray-900">{{ $allocation->allocation_category }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-extrabold text-gray-900">Rp {{ number_format($allocation->recommended_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $confidenceColor = $allocation->confidence_score >= 75 ? 'text-emerald-600 bg-emerald-50' : ($allocation->confidence_score >= 50 ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50');
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold {{ $confidenceColor }}">
                                        {{ $allocation->confidence_score }}%
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusColors = [
                                            'pending'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                        ];
                                        $statusColor = $statusColors[$allocation->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold border {{ $statusColor }} uppercase tracking-widest">
                                        {{ $allocation->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs text-gray-400 font-mono">{{ Str::limit($allocation->ai_model_used, 25) }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <a href="{{ route('admin.fund-allocation.show', $allocation) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 hover:underline transition-colors">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        </div>
                                        <p class="text-gray-400 italic text-sm">Belum ada rekomendasi. Klik "Jalankan Analisis AI" untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($allocations->hasPages())
                <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/10">
                    {{ $allocations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
