<div class="bg-white rounded-2xl p-3">
    <div class="flex gap-2 mb-4">
        <h2 class="bg-blueStatus w-fit border border-gradientBlue text-gradientBlue text-xs p-1 rounded-md">
            Feature
        </h2>
        <h2 class="bg-orangeStatus w-fit border border-gradientOrange text-gradientOrange text-xs p-1 rounded-md">
            High
        </h2>
    </div>

    <div class="border-b-2 pb-4 mb-4">
        <h3 class="text-sm">PROJ-001</h3>
        <h1 class="font-semibold text-lg font-lato mb-2">{{ $task->title }}</h1>
        <p class="text-xs text-slate-500 text-justify">
            {{ $task->description }}
        </p>
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div
                class="w-9 h-9 rounded-full overflow-hidden border border-slate-200 flex items-center justify-center bg-blue-50 text-blue-600 font-bold text-xs shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <p class="text-sm text-slate-500">
                @foreach ($task->humanResourceItems as $hr)
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($hr->teamMember->name, 0, 2)) }}
                        </div>
                        <span class="text-xs">{{ $hr->teamMember->name }}</span>
                    </div>
                @endforeach
            </p>
        </div>

        <p class="text-sm text-slate-500">
            31 Mei 2026
        </p>
    </div>
</div>
