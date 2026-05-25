<nav class="rounded-xl bg-cardSection h-full">
    <!-- Logo -->
    <div class="shrink-0 flex items-center p-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="hidden sm:-my-px sm:flex sm:flex-col sm:gap-2 sm:mt-6">
        <p class="text-xs ms-6">MENU</p>
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <i class="fas fa-desktop mr-2"></i>{{ __('Project Dashboard') }}
        </x-nav-link>
        
        <div x-data="{ open: {{ request()->routeIs('project-executing*') ? 'true' : 'false' }} }" class="ms-6 pr-8">

            {{-- Trigger --}}
            <button @click="open = !open"
                class="w-full inline-flex items-center justify-between text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                <span>
                    <i class="fas fa-users mr-2"></i>{{ __('Project Executing') }}
                </span>

                {{-- Arrow icon, rotate kalau open --}}
                <i class="fas fa-chevron-up text-xs transition-transform duration-200"
                    :class="open ? 'rotate-0' : 'rotate-180'"></i>
            </button>

            {{-- Dropdown items --}}
            <div x-show="open" x-transition class="border-l-2 border-gray-200 ml-6 mt-3 flex flex-col gap-2">
                <x-nav-link :href="route('teamManagement')" :active="request()->routeIs('teamManagement')">
                    {{ __('Team Management') }}
                </x-nav-link>

                <x-nav-link :href="route('taskManagement')" :active="request()->routeIs('taskManagement')">
                    {{ __('Task Management') }}
                </x-nav-link>

                <x-nav-link :href="route('kanbanBoard')" :active="request()->routeIs('kanbanBoard')">
                    {{ __('Kanban Board') }}
                </x-nav-link>
            </div>

        </div>
    </div>
</nav>
