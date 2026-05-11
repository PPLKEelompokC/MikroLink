<div class="flex flex-wrap items-center gap-2">
    <a href="{{ route('system-requirements.eligibility') }}" wire:navigate
        class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('system-requirements.eligibility') ? 'bg-amber-50 text-[#e8a838]' : 'bg-white text-gray-500 hover:bg-gray-50 hover:text-[#013599]' }} border border-gray-100 transition-colors">
        Skor Kelayakan
    </a>
    <a href="{{ route('system-requirements.approvals') }}" wire:navigate
        class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('system-requirements.approvals') ? 'bg-amber-50 text-[#e8a838]' : 'bg-white text-gray-500 hover:bg-gray-50 hover:text-[#013599]' }} border border-gray-100 transition-colors">
        Persetujuan
    </a>
    <a href="{{ route('system-requirements.allocation') }}" wire:navigate
        class="px-4 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('system-requirements.allocation') ? 'bg-amber-50 text-[#e8a838]' : 'bg-white text-gray-500 hover:bg-gray-50 hover:text-[#013599]' }} border border-gray-100 transition-colors">
        Alokasi Dana AI
    </a>
</div>
