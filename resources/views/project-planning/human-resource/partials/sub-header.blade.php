<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <div class="flex items-center text-xs gap-1 font-bold text-slate-400 uppercase tracking-wider">
                <a href="{{ route('project-planning.human-resource.index') }}"
                    class="inline-flex items-center font-bold hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left text-[8px]"></i>
                    {{ __('Kembali | ') }}
                </a>
                {{ $breadcrumb ?? '' }}
            </div>

            <h2 class="font-extrabold text-2xl text-slate-800 leading-tight mt-1">
                {{ $title }}
            </h2>

            @if (!empty($description))
                <p class="text-xs text-slate-500 mt-1">
                    {{ $description }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-2.5">
            {{-- Default button --}}
            <a href="{{ route('projects.show', $project->id) }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                <i class="fas fa-project-diagram text-slate-400"></i>
                {{ __('Hub Proyek') }}
            </a>

            {{-- Optional second button --}}
            @if (!empty($actionButtonEnabled) && $actionButtonEnabled)
                <a href="{{ route('projects.human-resource.edit', $project->id) }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                    <i class="fas fa-edit"></i>
                    Kelola Perencanaan
                </a>
            @endif
        </div>
    </div>
</div>
