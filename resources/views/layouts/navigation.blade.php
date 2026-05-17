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
        <x-nav-link :href="route('teamManagement')" :active="request()->routeIs('teamManagement')">
            <i class="fas fa-chart-line mr-2"></i>{{ __('Team Management') }}
        </x-nav-link>
    </div>
</nav>
