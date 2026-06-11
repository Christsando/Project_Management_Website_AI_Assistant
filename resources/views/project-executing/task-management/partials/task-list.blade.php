<div class="mt-4 bg-cardSection w-full h-fit rounded-2xl p-6">
    <h1 class="card-title text-black">Project Task List</h1>

    <table class="w-full mt-4">
        <thead class="border-b">
            <tr class="text-slate-400 text-sm border-b">
                <th class="w-10 px-2 text-center">No.</th>
                <th class="text-left px-2">Task</th>
                <th class="text-left px-2">Description</th>
                <th class="text-left px-2">Priority</th>
                <th class="w-40 px-2 text-center">Estimated Days</th>
                <th class="w-40 px-2 text-center">Due Date</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($tasks as $index => $task)
                <tr class="border-b hover:bg-slate-50 text-sm">
                    <td class="text-center py-2">{{ $index + 1 }}</td>
                    <td class="px-2 py-2">{{ $task->title }}</td>
                    <td class="px-2 py-2">{{ $task->description ?? '-' }}</td>
                    <td class="px-2 py-2">{{ $task->priority ?? '-' }}</td>
                    <td class="text-center py-2">{{ $task->estimated_days ?? '-' }}</td>
                    <td class="text-center py-2">
                        {{ optional($task->timelineItem)->end_date?->format('d M Y') ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-slate-400 text-sm">
                        Tidak ada task untuk kamu
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
