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
                <p class="text-xs text-secondaryText">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>
