<div class="grid grid-cols-4 w-full h-[350px] gap-4">

    @include('project-executing.task-management.partials.task-column', [
        'title' => 'To-Do',
        'color' => 'bg-black',
        'tasks' => $tasks['todo'] ?? []
    ])

    @include('project-executing.task-management.partials.task-column', [
        'title' => 'On-Going',
        'color' => 'bg-purple',
        'tasks' => $tasks['ongoing'] ?? []
    ])

    @include('project-executing.task-management.partials.task-column', [
        'title' => 'Done',
        'color' => 'bg-important',
        'tasks' => $tasks['done'] ?? []
    ])

    @include('project-executing.task-management.partials.task-column', [
        'title' => 'Approved',
        'color' => 'bg-green',
        'tasks' => $tasks['approved'] ?? []
    ])

</div>