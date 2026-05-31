<div class="card-background">
    <!-- Card title -->
    <div class="flex items-center justify-between">
        <span class="card-title text-black">{{ __('On Draft') }}</span>
        <button class="flex items-center justify-center rounded-full border border-black w-8 h-8">
            <i class="text-xs text-slate-500 fa-solid fa-arrow-up-right-from-square"></i>
        </button>
    </div>

    <!-- value or card content -->
    <div class="flex items-baseline justify-between mt-2 mb-2 ms-2">
        <span class="text-4xl font-black text-black tracking-tight">{{ $draftProjects }}</span>
    </div>

    <!-- progress of the content value -->
    <!-- change to dynamic data if fix -->
    <div class="flex gap-3 items-center text-sm text-blue-800">
        <p class="border border-blue-800 rounded-md px-2 flex items-center justify-center gap-1">
            0<i class="mt-0.5 fas fa-caret-up"></i>
        </p>
        <p>No drafted project this month</p>
    </div>
</div>