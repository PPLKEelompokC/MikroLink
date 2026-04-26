@extends('layouts.app')

@section('title', 'Detail Rekomendasi - MikroLink')

@section('content')
    <nav class="w-full h-[80px] flex justify-between items-center bg-white/80 backdrop-blur-md px-10 border-b border-[#e4e4e4] sticky top-0 z-50">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink Logo" class="w-[120px] h-auto">
            </a>
        </div>
        <div class="hidden lg:flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="font-bold text-[15px] text-gray-600 hover:text-[#e8a838] transition-colors">Dashboard</a>
            <a href="{{ route('admin.fund-allocation.index') }}" class="font-bold text-[15px] text-gray-600 hover:text-[#e8a838] transition-colors">AI Fund Allocation</a>
            <span class="font-bold text-[15px] text-[#e8a838]">Detail</span>
        </div>
        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm">
            {{ auth()->user()->initials() }}
        </div>
    </nav>

    <div class="w-full max-w-[1400px] mx-auto px-10 py-12 flex flex-col gap-8 relative z-10">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Back link --}}
        <a href="{{ route('admin.fund-allocation.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Details --}}
            <div class="lg:col-span-2 flex flex-col gap-8">
                {{-- Recommendation Card --}}
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $fundAllocation->allocation_category }}</h1>
                            <p class="text-sm text-gray-400 mt-1">Dibuat {{ $fundAllocation->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending'  => 'bg-amber-50 text-amber-600 border-amber-200',
                                'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                'rejected' => 'bg-red-50 text-red-600 border-red-200',
                            ];
                            $statusColor = $statusColors[$fundAllocation->status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                        @endphp
                        <span class="px-4 py-2 rounded-xl text-xs font-extrabold border {{ $statusColor }} uppercase tracking-widest">
                            {{ $fundAllocation->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-violet-50 to-indigo-50 rounded-2xl p-6">
                            <p class="text-[10px] font-bold text-violet-400 uppercase tracking-widest mb-2">Jumlah Rekomendasi</p>
                            <p class="text-2xl font-extrabold text-violet-700">Rp {{ number_format($fundAllocation->recommended_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2">Confidence Score</p>
                            <p class="text-2xl font-extrabold text-emerald-700">{{ $fundAllocation->confidence_score }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-2xl p-6">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">AI Model</p>
                            <p class="text-sm font-bold text-gray-700 font-mono break-all">{{ $fundAllocation->ai_model_used }}</p>
                        </div>
                    </div>

                    {{-- AI Reasoning --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Alasan & Analisis AI</h3>
                        <div class="bg-gray-50 rounded-2xl p-6 text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $fundAllocation->reasoning }}</div>
                    </div>
                </div>

                {{-- Snapshot Details --}}
                @if($fundAllocation->snapshot)
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Data Snapshot Finansial</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Saldo Kas</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($fundAllocation->snapshot->total_cash_balance, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pinjaman Outstanding</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($fundAllocation->snapshot->total_outstanding_loans, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cadangan Operasional</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($fundAllocation->snapshot->operational_reserve, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Deposit Pending</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($fundAllocation->snapshot->total_pending_deposits, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <p class="text-[10px] font-bold text-violet-400 uppercase tracking-widest">Dana Idle Tersedia</p>
                                <p class="text-xl font-extrabold text-violet-700 mt-1">Rp {{ number_format($fundAllocation->snapshot->idle_fund_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar: Actions --}}
            <div class="flex flex-col gap-6">
                @if($fundAllocation->status === 'pending' && auth()->user()->role === 'Manajer Koperasi')
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Tindakan</h3>
                        <div class="flex flex-col gap-3">
                            <form method="POST" action="{{ route('admin.fund-allocation.updateStatus', $fundAllocation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui Rekomendasi
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.fund-allocation.updateStatus', $fundAllocation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="w-full bg-white border-2 border-red-200 hover:bg-red-50 text-red-600 font-bold py-3 px-6 rounded-2xl transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak Rekomendasi
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Review Info --}}
                @if($fundAllocation->reviewed_by)
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Info Review</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Direview oleh</p>
                                <p class="text-sm font-bold text-gray-900 mt-1">{{ $fundAllocation->reviewer?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu Review</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $fundAllocation->reviewed_at?->translatedFormat('d F Y, H:i') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Koperasi Info --}}
                @if($fundAllocation->koperasi)
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Koperasi</h3>
                        <p class="text-sm font-bold text-gray-900">{{ $fundAllocation->koperasi->nama_koperasi }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $fundAllocation->koperasi->id_koperasi }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
