<x-app-layout>
    <x-slot name="header">
        <x-header-component :projects="$projects" :showSearch="true" mode="task" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-fit shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ __('TASK MANAGEMENT') }}
                </div>
                <h1 class="font-semibold text-3xl">{{ __('Manajamen Task') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Manage all your project task with us!') }}</p>
            </div>
            <!-- dropdown filter-->
            @include('project-executing.task-management.partials.filter')
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
                    @include('project-executing.task-management.partials.kanban-board', [
                        'tasks' => $allTasks->groupBy('status'),
                    ])
                </div>

                <div>
                    @include('project-executing.task-management.partials.task-list', [
                        'tasks' => $allTasksRaw,
                    ])
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

<script>
    const projectId = {{ $project->id ?? 'null' }};

    function showTaskInsight() {
        if (!projectId) return;

        fetch(`/task-insight/${projectId}`)
            .then(res => res.json())
            .then(res => {

                if (!res.success) {
                    console.log(res.message);
                    return;
                }

                const toast = document.createElement('div');
                toast.className = `
                    fixed bottom-4 right-4 
                    bg-white shadow-lg 
                    p-4 rounded-lg w-96 
                    border-l-4 border-red-500 
                    z-50
                `;

                toast.innerHTML = `
                    <div class="font-semibold text-sm mb-1">
                        Task Insight Summary
                    </div>

                    <p class="text-sm text-slate-700 text-justify">
                        ${res.message}
                    </p>

                    <button 
                        onclick="this.parentElement.remove()" 
                        class="text-xs mt-3 text-blue-500 hover:underline"
                    >
                        Tutup
                    </button>
                `;

                document.body.appendChild(toast);

                // optional: auto close after 10s
                setTimeout(() => {
                    toast.remove();
                }, 100000);

            })
            .catch(err => {
                console.error('Task insight error:', err);
            });
    }

    window.addEventListener('DOMContentLoaded', () => {
        showTaskInsight();
    });
</script>