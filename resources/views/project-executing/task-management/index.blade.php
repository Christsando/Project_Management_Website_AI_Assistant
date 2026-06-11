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
            <div class="text-center py-10 text-slate-400">
                <p class="text-sm font-semibold">Belum ada project dipilih</p>
                <p class="text-xs mt-1">Silakan pilih project melalui search di atas</p>
            </div>
        @else
            @include('project-executing.task-management.partials.kanban-board', [
                'tasks' => $allTasks->groupBy('status'),
            ])

            @include('project-executing.task-management.partials.task-list', [
                'tasks' => $myTasks,
            ])
        @endif
    </div>
</x-app-layout>
