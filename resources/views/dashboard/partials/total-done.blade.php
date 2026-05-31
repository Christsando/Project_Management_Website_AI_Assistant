<div class="border border-slate-200 bg-gradient-to-br from-green to-gradientGreen rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
    <!-- Card title -->
    <div class="flex items-center justify-between">
        <span class="card-title">{{ __('Done') }}</span>
        <button class="flex items-center justify-center rounded-full bg-white w-8 h-8">
            <i class="text-xs text-slate-500 fa-solid fa-arrow-up-right-from-square"></i>
        </button>
    </div>
    
    <!-- value or card content -->
    <div class="flex items-baseline justify-between mt-2 mb-2 ms-2">
        <span class="text-4xl font-black text-white tracking-tight">{{ $completedProjects }}</span>
    </div>

    <!-- progress of the content value -->
    <!-- change to dynamic data if fix -->
    <div class="flex gap-3 items-center text-sm text-white">
        <p class="border rounded-md px-2 flex items-center justify-center gap-1">
            0<i class="mt-0.5 fas fa-caret-up"></i>
        </p>
        <p>No project done this month</p>
    </div>
</div>