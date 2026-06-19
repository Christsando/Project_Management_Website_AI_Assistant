<div class="mt-4 bg-cardSection w-full rounded-2xl p-6">
    <h1 class="card-title text-black">Project Task List</h1>

    <!-- SCROLL WRAPPER (INI KUNCINYA) -->
    <div class="mt-4 max-h-[260px] overflow-y-auto">

        <table class="w-full">
            
            <!-- HEADER (STICKY) -->
            <thead class="border-b sticky top-0 bg-cardSection z-10">
                <tr class="text-slate-400 text-sm border-b">
                    <th class="w-10 px-2 text-center">No.</th>
                    <th class="text-left px-2">Task</th>
                    <th class="text-left px-2">Description</th>
                    <th class="px-2 text-center">Priority</th>
                    <th class="w-40 px-2 text-center">Estimated Days</th>
                    <th class="w-40 px-2 text-center">Due Date</th>
                    <th class="w-24 px-2 text-center">Finished</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody>
                @forelse ($tasks as $index => $task)
                    <tr class="border-b hover:bg-slate-50 text-sm">
                        <td class="text-center py-2">{{ $index + 1 }}</td>
                        <td class="px-2 py-2">{{ $task->title }}</td>
                        <td class="px-2 py-2">{{ $task->description ?? '-' }}</td>

                        <td class="px-2 py-2 text-center text-white">
                            <span class="
                                w-full inline-block py-1 rounded-md text-sm
                                @if($task->priority == 'low')
                                    bg-green-300 border border-green-700 text-green-900
                                @elseif($task->priority == 'medium')
                                    bg-orangeStatus border border-gradientOrange text-gradientOrange
                                @elseif($task->priority == 'high')
                                    bg-red-400 border border-red-700 text-red-900
                                @else
                                    bg-gray-400
                                @endif
                            ">
                                {{ $task->priority ?? '-' }}
                            </span>
                        </td>

                        <td class="text-center py-2">
                            @php
                                $dueDate = optional($task->timelineItem)->end_date;
                            @endphp

                            @if($task->kanban_status == "approved")
                                -
                            @elseif ($dueDate)
                                {{ (int) now()->diffInDays($dueDate, false) }} hari
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center py-2">{{ optional($task->timelineItem)->end_date?->format('d M Y') ?? '-' }}</td>

                        <td class="text-center py-2 align-middle">
                            @if($task->kanban_status === 'approved')
                                <span class="w-full inline-block rounded-md text-sm py-1 bg-blueStatus border border-gradientBlue text-gradientBlue">
                                    Approved
                                </span>
                                <span class="text-xs text-slate-400">
                                    By: {{ $task->statusUpdater->name ?? '-' }}
                                    ({{ $task->statusUpdater->role ?? '-' }})
                                </span>
                            @elseif($task->task_status_finished_at)
                                {{ $task->task_status_finished_at->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-slate-400 text-sm">Tidak ada task untuk kamu</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>
</div>