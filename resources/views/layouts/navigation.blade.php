<nav class="rounded-2xl bg-white border border-slate-100 shadow-sm h-full flex flex-col justify-between p-5">
    <div>
        <!-- Logo -->
        <div class="flex items-center px-2 py-4 mb-4">
            <div class="bg-blue-600 rounded-xl p-2 flex items-center justify-center shadow-md shadow-blue-500/20 mr-2.5">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-blue-900">KelolaIN</span>
        </div>

        <!-- Create Project Button (PM Only) -->
        @if (Auth::check() && strtolower(Auth::user()->role) === 'project manager')
            <div class="px-2 mb-6">
                <a href="{{ route('projects.create') }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition shadow-md shadow-blue-500/10 hover:shadow-lg">
                    <i class="fas fa-plus text-[10px]"></i>
                    <span>{{ __('Buat Proyek Baru') }}</span>
                </a>
            </div>
        @endif

        <!-- Menu Links -->
        <div class="flex flex-col gap-1.5 px-2">
            <!-- Dashboard Link -->
            <x-nav-link :active="request()->routeIs('dashboard')" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-table-columns text-base"></i>
                <span>{{ __('Dashboard') }}</span>
            </x-nav-link>

            <!-- Inisiasi Proyek Link (PM & Manager) -->
            @if (Auth::check() && in_array(strtolower(Auth::user()->role), ['project manager', 'manager']))
                <x-nav-link :active="request()->routeIs('projects.index')" href="{{ route('projects.index') }}">
                    <i class="fas fa-rocket text-base"></i>
                    <span>{{ __('Inisiasi Proyek') }}</span>
                </x-nav-link>
            @endif

            <!-- Perencanaan Proyek Link (Manager & PMO) -->
            @if (Auth::check() && in_array(strtolower(Auth::user()->role), ['manager', 'project management officer', 'pmo']))
                <x-nav-link :active="request()->routeIs('project-planning')" href="{{ route('project-planning') }}">
                    <i class="fa-regular fa-calendar text-base"></i>
                    <span>{{ __('Perencanaan Proyek') }}</span>
                </x-nav-link>
            @endif

            <!-- Manajemen Tim Link -->
            <x-nav-link :active="request()->routeIs('teamManagement')" href="{{ route('teamManagement') }}">
                <i class="fas fa-users text-base"></i>
                <span>{{ __('Manajemen Tim') }}</span>
            </x-nav-link>

            <!-- Manajemen Tim Link -->
            <x-nav-link :active="request()->routeIs('taskManagement')" href="{{ route('taskManagement') }}">
                <i class="fas fa-tasks text-base"></i>
                <span>{{ __('Manajemen Task') }}</span>
            </x-nav-link>
        </div>
    </div>

    <!-- Bottom Menu -->
    <div class="px-2 pt-4 border-t border-slate-100 flex flex-col gap-1.5">
        <!-- Settings Link -->
        <x-nav-link :active="request()->routeIs('profile.edit')" href="{{ route('profile.edit') }}">
            <i class="fa-solid fa-gear text-base"></i>
            <span>{{ __('Pengaturan') }}</span>
        </x-nav-link>

        <!-- Logout Link -->
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-600 hover:bg-rose-50 hover:text-rose-600">
            <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
            <span>{{ __('Keluar') }}</span>
        </a>
    </div>
</nav>
