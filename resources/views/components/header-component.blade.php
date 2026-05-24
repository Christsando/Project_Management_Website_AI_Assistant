<div class="flex rounded-xl bg-cardSection p-5 items-center justify-between">
    <!-- Search bar -->
    <div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Notification -->
        <a href="#">
            <div class="flex items-center justify-center bg-white rounded-2xl w-8 h-8">
                <i class="fas fa-bell"></i>
            </div>
        </a>

        <!-- User avatar -->
        <div class="flex gap-2 items-center">
            <div class="flex items-center justify-center w-10 h-10 bg-important 
            font-semibold font-lato text-sm border border-white border-2 
            text-white tracking-widest rounded-3xl">
                <h1>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</h1>
            </div>

            <!-- User detail -->
            <div class="font-lato tracking-wide">
                <p class="text-sm">{{ Auth::user()->name }}</p>
                <div class="flex items-center gap-2">
                    <p class="text-xs text-secondaryText">{{ Auth::user()->email }}</p>
                    <span class="text-gray-300 text-xs">|</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 hover:underline inline-flex items-center gap-1 font-semibold">
                            <i class="fas fa-sign-out-alt text-[10px]"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
