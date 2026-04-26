<x-layouts.app.header title="Detail Tiket - {{ $ticket->ticket_number }}">
<div class="min-h-screen bg-gray-50" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo-mikrolink.png') }}" alt="MikroLink" class="h-10 w-auto"
                onerror="this.style.display='none'; document.getElementById('nav-logo-fb3').style.display='flex';">
            <div id="nav-logo-fb3" class="hidden items-center gap-1">
                <div class="w-8 h-8 rounded-full bg-green-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                </div>
                <span class="font-bold text-green-800 text-lg">MikroLink</span>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition-colors">Dashboard</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Loan Services</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Verifikasi &amp; Keanggotaan</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Report &amp; Analytics</a>
            <a href="{{ route('education-center') }}" class="font-semibold text-yellow-500 hover:text-yellow-600 transition-colors">Education Center</a>
        </div>
        <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center cursor-pointer text-sm font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </nav>

    {{-- HERO --}}
    <div class="bg-white border-b border-gray-100 px-6 py-10 relative overflow-hidden">
        <div class="relative max-w-5xl mx-auto">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-3">
                <a href="{{ route('education-center') }}" class="hover:text-yellow-500 transition-colors">Education Center</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('ticketing.index') }}" class="hover:text-yellow-500 transition-colors">Tiket Saya</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium font-mono">{{ $ticket->ticket_number }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 flex-1">{{ $ticket->subject }}</h1>
                @php
                    $sColors = ['open' => 'bg-blue-50 text-blue-600 border-blue-200', 'in_progress' => 'bg-yellow-50 text-yellow-600 border-yellow-200', 'resolved' => 'bg-green-50 text-green-600 border-green-200', 'closed' => 'bg-gray-100 text-gray-500 border-gray-200'];
                @endphp
                <span class="inline-flex items-center gap-2 text-sm px-4 py-1.5 rounded-full font-semibold border {{ $sColors[$ticket->status] ?? 'bg-gray-100 text-gray-500 border-gray-200' }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span>
                    {{ $ticket->status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-6">

            {{-- MAIN CONTENT --}}
            <div class="md:col-span-2 space-y-5">

                {{-- Deskripsi --}}
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h2 class="font-bold text-gray-900 text-sm">Deskripsi Masalah</h2>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $ticket->description }}</p>
                    </div>
                </div>

                {{-- Lampiran --}}
                @if($ticket->attachment)
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <h2 class="font-bold text-gray-900 text-sm">Lampiran</h2>
                    </div>
                    <div class="px-6 py-4">
                        <a href="{{ Storage::url($ticket->attachment) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-yellow-600 hover:text-yellow-700 font-medium border border-yellow-200 bg-yellow-50 px-4 py-2 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Lihat Lampiran
                        </a>
                    </div>
                </div>
                @endif

                {{-- Timeline --}}
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h2 class="font-bold text-gray-900 text-sm">Riwayat Tiket</h2>
                    </div>
                    <div class="px-6 py-5">
                        <div class="relative">
                            <div class="absolute left-3.5 top-6 bottom-0 w-0.5 bg-gray-100"></div>
                            <div class="space-y-5">
                                {{-- Created --}}
                                <div class="flex items-start gap-4">
                                    <div class="w-7 h-7 rounded-full bg-yellow-400 flex items-center justify-center flex-shrink-0 z-10">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Tiket dibuat</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</p>
                                        <p class="text-xs text-gray-500 mt-1">Tiket berhasil dikirim dan menunggu penanganan tim support.</p>
                                    </div>
                                </div>
                                {{-- Status --}}
                                @if($ticket->status !== 'open')
                                <div class="flex items-start gap-4">
                                    <div class="w-7 h-7 rounded-full bg-blue-400 flex items-center justify-center flex-shrink-0 z-10">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Tiket sedang diproses</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->updated_at->format('d M Y, H:i') }} WIB</p>
                                        <p class="text-xs text-gray-500 mt-1">Tim support sedang menangani masalah Anda.</p>
                                    </div>
                                </div>
                                @endif
                                @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                                <div class="flex items-start gap-4">
                                    <div class="w-7 h-7 rounded-full bg-green-400 flex items-center justify-center flex-shrink-0 z-10">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Tiket diselesaikan</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->updated_at->format('d M Y, H:i') }} WIB</p>
                                        <p class="text-xs text-gray-500 mt-1">Masalah telah berhasil diselesaikan oleh tim support.</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('ticketing.index') }}"
                       class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 font-semibold px-5 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                    <a href="{{ route('ticketing.create') }}"
                       class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                        Buat Tiket Baru
                    </a>
                </div>
            </div>

            {{-- SIDEBAR INFO --}}
            <div class="space-y-4">

                {{-- Detail Tiket --}}
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 text-sm mb-4">Informasi Tiket</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">No. Tiket</span>
                            <span class="font-mono font-semibold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-lg">{{ $ticket->ticket_number }}</span>
                        </div>
                        <div class="border-t border-gray-50 pt-3 flex justify-between items-center">
                            <span class="text-gray-500">Kategori</span>
                            <span class="font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $ticket->category_label }}</span>
                        </div>
                        <div class="border-t border-gray-50 pt-3 flex justify-between items-center">
                            <span class="text-gray-500">Prioritas</span>
                            @php
                                $pColors2 = ['low' => 'bg-green-50 text-green-600', 'medium' => 'bg-yellow-50 text-yellow-600', 'high' => 'bg-red-50 text-red-600'];
                            @endphp
                            <span class="font-medium px-2 py-0.5 rounded-lg {{ $pColors2[$ticket->priority] ?? 'bg-gray-100 text-gray-600' }}">{{ $ticket->priority_label }}</span>
                        </div>
                        <div class="border-t border-gray-50 pt-3 flex justify-between items-center">
                            <span class="text-gray-500">Status</span>
                            @php
                                $sColors2 = ['open' => 'bg-blue-50 text-blue-600', 'in_progress' => 'bg-yellow-50 text-yellow-600', 'resolved' => 'bg-green-50 text-green-600', 'closed' => 'bg-gray-100 text-gray-500'];
                            @endphp
                            <span class="inline-flex items-center gap-1 font-medium px-2 py-0.5 rounded-lg {{ $sColors2[$ticket->status] ?? 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $ticket->status_label }}
                            </span>
                        </div>
                        <div class="border-t border-gray-50 pt-3 flex justify-between items-start">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="font-medium text-gray-700 text-right">{{ $ticket->created_at->format('d M Y') }}<br>{{ $ticket->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="border-t border-gray-50 pt-3 flex justify-between items-start">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="font-medium text-gray-700 text-right">{{ $ticket->updated_at->format('d M Y') }}<br>{{ $ticket->updated_at->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                {{-- Pesan --}}
                @if($ticket->status === 'open')
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-blue-700">Tiket Anda sedang menunggu penanganan. Estimasi respon <strong>1–3 hari kerja</strong>.</p>
                    </div>
                </div>
                @elseif($ticket->status === 'resolved')
                <div class="bg-green-50 border border-green-100 rounded-2xl p-5">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-green-700">Tiket telah <strong>diselesaikan</strong>. Jika masalah masih berlanjut, buat tiket baru.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.app.header>