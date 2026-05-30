@props(['title' => null, 'icon' => null])

<div class="flex bg-white border border-slate-100 shadow-sm rounded-2xl p-4 items-center justify-between mb-4">
    <!-- Left Section: Title (if present) or default search bar -->
    <div class="flex items-center gap-3">
        @if ($title)
            @if ($icon)
                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                    <i class="{{ $icon }}"></i>
                </div>
            @endif
            <div>
                <h1 class="text-sm font-bold text-slate-800 tracking-tight">{{ $title }}</h1>
                <p class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Sistem Manajemen Proyek</p>
            </div>
        @else
            <div class="relative w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-slate-400 text-sm"></i>
                </span>
                <input type="text" placeholder="Cari proyek, tugas, atau berkas..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-100/60 border border-slate-200/50 rounded-full text-xs text-slate-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-400"
                    readonly>
            </div>
        @endif
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-5">
        <!-- Search bar on the right if title is present -->
        @if ($title)
            <div class="relative w-48 hidden sm:block">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                </span>
                <input type="text" placeholder="Cari dokumen..."
                    class="w-full pl-8 pr-3 py-1.5 bg-slate-100/60 border border-slate-200/50 rounded-full text-[11px] text-slate-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-400"
                    readonly>
            </div>
        @endif

        <!-- Notification -->
        <a href="#" class="relative p-2 text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fa-regular fa-bell text-lg"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full border border-white"></span>
        </a>

        <!-- User profile -->
        <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
            <div class="text-right hidden md:block">
                <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] font-semibold text-slate-400 mt-0.5">{{ Auth::user()->role }}</p>
            </div>
            <!-- Avatar circle -->
            <div
                class="w-9 h-9 rounded-full overflow-hidden border border-slate-200 flex items-center justify-center bg-blue-50 text-blue-600 font-bold text-xs shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>

            <form method="POST" action="{{ route('logout') }}" class="hidden" id="header-logout-form">
                @csrf
            </form>
        </div>
    </div>
</div>
