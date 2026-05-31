<div class="flex flex-col gap-4 bg-cardSection p-4 rounded-md">
    <div class="flex items-center border-b-2 pt-2 pb-4">
        <div class="rounded-full w-2 h-2 {{ $color }} mr-2"></div>
        <p class="text-slate-500 text-base">{{ $title }}</p>
    </div>

    @foreach ($tasks as $task)
        @include('project-executing.task-management.partials.task-card', ['task' => $task])
    @endforeach
</div>