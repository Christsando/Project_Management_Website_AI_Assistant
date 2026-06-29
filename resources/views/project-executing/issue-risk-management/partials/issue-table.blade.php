<div class="max-h-full overflow-y-auto">
    <table class="w-full">
        <thead class="border-b-2 border-slate-400 sticky top-0 bg-white">
            <tr class="text-slate-400">
                <td class="w-32 text-center py-2">Status</td>
                <td class="w-40 text-center py-2">Key</td>
                <td class="w-60 text-left py-2">Title</td>
                <td class="py-2">Summary</td>
                <td class="w-40 py-2">Assigned</td>
                <td class="w-40 py-2">Report By</td>
            </tr>
        </thead>

        <tbody>
            @forelse($issues as $issue)
                <tr class="border-b text-sm hover:bg-slate-100 cursor-pointer" onclick='openDetailModal(@json($issue))'>
                    <td class="text-center py-4">{{ $issue->status }}</td>
                    <td class="text-center py-4">ISS-{{ $issue->id }}</td>
                    <td class="py-4">{{ $issue->title }}</td>
                    <td class="py-4">{{ $issue->description }}</td>
                    <td class="py-4">{{ $issue->assignee->name ?? '-' }}</td>
                    <td class="py-4">{{ $issue->reporter->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-40 text-slate-400">
                        <i class="fas fa-file text-5xl"></i>
                        <p class="text-xl font-semibold">Belum ada issue</p>
                        <p class="text-sm">Silakan membuat issue terlebih dahulu</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    let currentIssueId = null;
    function openDetailModal(issue) {
        currentIssueId = issue.id;

        document.getElementById('detail-title').innerText = issue.title ?? '-';
        document.getElementById('detail-description').innerText = issue.description ?? '-';
        document.getElementById('detail-status').value = issue.status ?? 'open';
        document.getElementById('detail-priority').innerText = issue.priority ?? '-';
        document.getElementById('detail-assignee').innerText = issue.assignee?.name ?? '-';
        document.getElementById('detail-reporter').innerText = issue.reporter?.name ?? '-';
        document.getElementById('detail-due').innerText = issue.due_date ?? '-';

        const modal = document.getElementById('issue-detail-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailModal() {
        const modal = document.getElementById('issue-detail-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // klik luar modal = close
    document.getElementById('issue-detail-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
</script>
