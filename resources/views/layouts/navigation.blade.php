<nav class="rounded-xl bg-cardSection h-full">
    <!-- Logo -->
    <div class="shrink-0 flex items-center p-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="hidden sm:-my-px sm:ms-5 sm:flex sm:flex-col sm:gap-4">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <i class="fas fa-chart-line mr-2"></i>{{ __('Project Dashboard') }}
        </x-nav-link>

        @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['project manager', 'manager']))
        <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*') || request()->routeIs('project-initiation')">
            <i class="fas fa-folder-plus mr-2"></i>{{ __('Project Initiation') }}
        </x-nav-link>
        @endif

        @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['manager', 'project management officer', 'pmo']))
        <x-nav-link :href="route('project-planning')" :active="request()->routeIs('project-planning')">
            <i class="fas fa-tasks mr-2"></i>{{ __('Project Planning') }}
        </x-nav-link>
        @endif

        <x-nav-link :href="route('teamManagement')" :active="request()->routeIs('teamManagement')">
            <i class="fas fa-users mr-2"></i>{{ __('Team Management') }}
        </x-nav-link>
    </div>
</nav>
