<x-layouts.app.header title="Pusat Bantuan - Ticketing">
<div class="min-h-screen bg-gray-50" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/mikrolink-logo.png') }}" alt="MikroLink" class="h-10 w-auto"
                onerror="this.style.display='none'; document.getElementById('nav-logo-fb').style.display='flex';">
            <div id="nav-logo-fb" class="hidden items-center gap-1">
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
            <a href="{{ route('pusat-bantuan') }}" class="font-semibold text-yellow-500 hover:text-yellow-600 transition-colors">Pusat Bantuan</a>
        </div>
        <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center cursor-pointer text-sm font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </nav>

    {{-- HERO --}}
    <div class="bg-white border-b border-gray-100 px-6 py-10 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            @for ($i = 0; $i < 10; $i++)
                <div class="absolute w-8 h-8 border border-gray-200 rotate-45 opacity-30"
                     style="left:{{ ($i * 11) % 100 }}%; top:{{ ($i * 17) % 100 }}%;"></div>
            @endfor
        </div>
        <div class="relative max-w-5xl mx-auto">
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-3">
                <a href="{{ route('pusat-bantuan') }}" class="hover:text-yellow-500 transition-colors">Pusat Bantuan</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium">Tiket Saya</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Pusat Bantuan &amp; Ticketing</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola tiket support Anda dan pantau status penyelesaiannya</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">

        @if(session('success'))
        <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 text-sm">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- STATS CARDS --}}
        @php
            $statTotal    = \App\Models\Ticket::where('user_id', auth()->id())->count();
            $statOpen     = \App\Models\Ticket::where('user_id', auth()->id())->where('status', 'open')->count();
            $statProcess  = \App\Models\Ticket::where('user_id', auth()->id())->where('status', 'in_progress')->count();
            $statResolved = \App\Models\Ticket::where('user_id', auth()->id())->where('status', 'resolved')->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500">Total Tiket</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-700">{{ $statTotal }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500">Terbuka</span>
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ $statOpen }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500">Diproses</span>
                    <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-yellow-600">{{ $statProcess }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500">Selesai</span>
                    <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ $statResolved }}</p>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Daftar Tiket Saya</h2>
                <a href="{{ route('ticketing.create') }}"
                   class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Buat Tiket Baru
                </a>
            </div>

            @if($tickets->isEmpty())
            <div class="py-20 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400 text-sm mb-5">Belum ada tiket. Buat tiket pertama Anda!</p>
                <a href="{{ route('ticketing.create') }}"
                   class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Buat Tiket
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Tiket</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subjek</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-lg">
                                    {{ $ticket->ticket_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800 max-w-xs truncate">{{ $ticket->subject }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                                    {{ $ticket->category_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $pColors = ['low' => 'bg-green-50 text-green-600', 'medium' => 'bg-yellow-50 text-yellow-600', 'high' => 'bg-red-50 text-red-600'];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $pColors[$ticket->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $sColors = ['open' => 'bg-blue-50 text-blue-600', 'in_progress' => 'bg-yellow-50 text-yellow-600', 'resolved' => 'bg-green-50 text-green-600', 'closed' => 'bg-gray-100 text-gray-500'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium {{ $sColors[$ticket->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                                {{ $ticket->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('ticketing.show', $ticket) }}"
                                   class="inline-flex items-center gap-1 text-xs text-yellow-600 hover:text-yellow-700 font-semibold transition-colors">
                                    Detail
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
            @endif
            @endif
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-100 rounded-xl px-5 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-yellow-700">Tim support kami akan merespon tiket Anda dalam <strong>1–3 hari kerja</strong>. Untuk keperluan mendesak, hubungi kami via WhatsApp atau Call Center.</p>
        </div>
    </div>
</div>
</x-layouts.app.header>