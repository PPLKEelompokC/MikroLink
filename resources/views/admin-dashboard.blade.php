<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MikroLink</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f9fafb; margin: 0; }

        .bg-fade-wrapper {
            position: absolute; inset: 0; z-index: -1;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 50%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 50%);
        }

        .diamond-pattern {
            width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='160' viewBox='0 0 300 160'%3E%3Cpath d='M150 0 L300 80 L150 160 L0 80 Z' fill='%23e4e7ec' fill-opacity='0.4' /%3E%3C/svg%3E");
            background-size: 320px 170px; background-position: center top;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col relative overflow-x-hidden">

    <div class="bg-fade-wrapper"><div class="diamond-pattern"></div></div>

    <nav class="w-full h-[80px] flex justify-between items-center bg-[#013599] px-12 border-b border-[#012a7a] sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="font-extrabold text-white text-xl tracking-tight">
                MikroLink <span class="text-[#ffa200]">Admin</span>
            </a>
            <div class="h-6 w-[1px] bg-white/20"></div>
            <span class="font-bold text-white/80 text-sm tracking-tight uppercase">Manajemen Pusat Data</span>
        </div>
        <div x-data="{ open: false }" class="relative flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold text-[#ffa200] uppercase tracking-widest">Administrator</p>
                <p class="text-sm font-extrabold text-white">{{ Auth::user()->name }}</p>
            </div>
            
            <button @click="open = !open" @click.away="open = false" class="focus:outline-none flex items-center gap-2">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#013599] font-black text-sm hover:scale-105 transition-transform shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </button>

            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50" style="display: none;">
                <div class="px-4 py-2 border-b border-gray-50 sm:hidden">
                    <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#013599] transition-colors">Edit Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-[1400px] mx-auto w-full px-12 py-12">

        @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl flex items-center gap-4 text-sm font-bold shadow-sm">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        @endif
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-[32px] font-extrabold text-black tracking-tight mb-2">Rekapitulasi Aspirasi Warga</h1>
                <p class="text-gray-500 font-medium">Tinjau, setujui, atau tolak permohonan dukungan mandiri dari para anggota.</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center gap-6">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Entri</p>
                    <p class="text-2xl font-black text-[#013599]">{{ $aspirations->count() }}</p>
                </div>
                <div class="h-10 w-[1px] bg-gray-100"></div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Menunggu</p>
                    <p class="text-2xl font-black text-[#e8a838]">{{ $aspirations->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <section class="bg-white border border-gray-100 rounded-[32px] overflow-hidden shadow-xl shadow-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-5">Tgl Masuk</th>
                            <th class="px-8 py-5">Pengaju (Warga)</th>
                            <th class="px-8 py-5">Konteks Aspirasi</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-right w-[200px]">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($aspirations as $aspiration)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">{{ $aspiration->created_at->format('d/m/Y') }}</td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-gray-900">{{ $aspiration->user->name ?? 'User Terhapus' }}</p>
                                <p class="text-xs text-gray-400 font-medium">{{ $aspiration->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-[#013599]">{{ $aspiration->subject }}</p>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm leading-relaxed line-clamp-2" title="{{ $aspiration->message }}">{{ $aspiration->message }}</p>
                            </td>
                            <td class="px-8 py-5">
                                @if($aspiration->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[11px] font-bold rounded-full uppercase border border-yellow-200">Pending</span>
                                @elseif($aspiration->status == 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded-full uppercase border border-green-200">Approved</span>
                                @elseif($aspiration->status == 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-[11px] font-bold rounded-full uppercase border border-red-200">Rejected</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if($aspiration->status == 'pending')
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.aspiration.update', $aspiration->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition-transform hover:scale-105 shadow-sm" title="Setujui">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.aspiration.update', $aspiration->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-transform hover:scale-105 shadow-sm" title="Tolak">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <p class="text-xs font-bold text-gray-400 text-right uppercase">Terproses</p>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-gray-400 font-bold text-sm">Belum ada aspirasi dari warga.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>