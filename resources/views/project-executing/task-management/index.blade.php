<x-app-layout>
    <x-slot name="header">
        <x-header-component :projects="$projects" :showSearch="true" mode="task" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-full shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                {{ __('TASK MANAGEMENT') }}
            </div>
            <h1 class="font-semibold text-3xl">{{ __('Manajamen Task') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage all your project task with us!') }}</p>
        </div>

        @if (empty($project))
            <div class="text-center py-32 text-slate-400">
                <i class="fas fa-folder text-5xl"></i>
                <p class="text-xl font-semibold">Belum ada project dipilih</p>
                <p class="text-sm">Silakan pilih project melalui search di atas</p>
            </div>
        @else
            <div class="flex flex-col gap-1">
                <div>
                    @include('project-executing.task-management.partials.kanban-board', ['tasks' => $allTasks->groupBy('status'),])
                </div>

                <div>
                    @include('project-executing.task-management.partials.task-list', ['tasks' => $allTasksRaw,])
                </div>
            </div>
        @endif
    </div>
</x-app-layout>