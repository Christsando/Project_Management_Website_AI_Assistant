<div class="bg-cardSection p-4 rounded-md flex flex-col h-[500px]">

    <!-- HEADER -->
    <div class="flex items-center border-b-2 pt-2 pb-4 flex-shrink-0">
        <div class="rounded-full w-2 h-2 {{ $color }} mr-2"></div>
        <p class="text-slate-500 text-base capitalize">
            {{ $title }}
        </p>

        <span class="ml-auto text-xs bg-slate-100 px-2 py-1 rounded">
            {{ count($tasks) }}
        </span>
    </div>

    <!-- LIST (INI YANG DI DRAG) -->
    <div class="flex-1 overflow-y-auto mt-2 task-list" data-status="{{ $status }}">
        @forelse ($tasks as $task)
            @include('project-executing.task-management.partials.task-card', ['task' => $task])
        @empty
            <div class="text-center text-xs text-slate-400 py-6">
                No tasks
            </div>
        @endforelse
    </div>
</div>