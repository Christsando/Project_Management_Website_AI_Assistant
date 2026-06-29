<div class="max-h-full overflow-y-auto">
    <table class="w-full">
        <thead class="border-b-2 border-slate-400 sticky top-0 bg-white">
            <tr class="text-slate-400">
                <td class="w-20 text-center py-2">No</td>
                <td class="w-60 py-2">Title</td>
                <td class="w-60 text-left py-2">Description</td>
                <td class="w-20 py-2 text-center">Probability</td>
                <td class="w-20 py-2 text-center">Severity</td>
                <td class="w-20 py-2 text-center">Status</td>
                <td class="w-40 py-2 text-center">Risk Owner</td>
            </tr>
        </thead>

        <tbody>
            @forelse($risks as $index => $risk)
                <tr class="border-b text-sm hover:bg-slate-100 cursor-pointer"
                    onclick='openRiskDetail(@json($risk))'>
                    <td class="text-center py-4">{{ $index + 1 }}</td>
                    <td class="py-4 font-medium">{{ $risk->risk_title }}</td>
                    <td class="py-4">{{ Str::limit($risk->risk_description, 70) }}</td>
                    <td class="py-4 text-center">
                        <span
                            class="px-2 py-1 rounded-full text-xs
                            @if ($risk->probability == 'high') bg-red-100 text-red-700
                            @elseif($risk->probability == 'medium')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-green-100 text-green-700 @endif">
                            {{ ucfirst($risk->probability) }}
                        </span>
                    </td>
                    <td class="py-4 text-center">
                        <span
                            class="px-2 py-1 rounded-full text-xs
                            @if ($risk->severity == 'high') bg-red-100 text-red-700
                            @elseif($risk->severity == 'medium')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-green-100 text-green-700 @endif">
                            {{ ucfirst($risk->severity) }}
                        </span>
                    </td>
                    <td class="py-4 text-center">
                        <span
                            class="px-2 py-1 rounded-full text-xs
                            @if ($risk->status == 'open') bg-blue-100 text-blue-700
                            @elseif($risk->status == 'closed')
                                bg-green-100 text-green-700
                            @else
                                bg-slate-100 text-slate-700 @endif">
                            {{ ucfirst($risk->status) }}
                        </span>
                    </td>
                    <td class="py-4">{{ $risk->risk_owner }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-40 text-slate-400">
                        <i class="fas fa-shield-halved text-5xl"></i>
                        <p class="text-xl font-semibold">Belum ada risk</p>
                        <p class="text-sm">Silakan membuat risk terlebih dahulu</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    let currentRiskId = null;

    function openRiskDetail(risk) {

        currentRiskId = risk.id;

        document.getElementById('risk-title').innerText = risk.risk_title ?? '-';
        document.getElementById('risk-description').innerText = risk.risk_description ?? '-';
        document.getElementById('risk-cause').innerText = risk.risk_cause ?? '-';
        document.getElementById('risk-impact').innerText = risk.impact ?? '-';
        document.getElementById('risk-mitigation').innerText = risk.mitigation_plan ?? '-';
        document.getElementById('risk-contingency').innerText = risk.contingency_plan ?? '-';
        document.getElementById('risk-owner').innerText = risk.risk_owner ?? '-';
        document.getElementById('risk-notes').innerText = risk.notes ?? '-';
        const probability = document.getElementById('risk-probability');
        const severity = document.getElementById('risk-severity');
        const status = document.getElementById('risk-status');

        setBadge(probability, risk.probability, {
            high: ['bg-red-100', 'text-red-700'],
            medium: ['bg-yellow-100', 'text-yellow-700'],
            low: ['bg-green-100', 'text-green-700']
        });

        setBadge(severity, risk.severity, {
            high: ['bg-red-100', 'text-red-700'],
            medium: ['bg-yellow-100', 'text-yellow-700'],
            low: ['bg-green-100', 'text-green-700']
        });

        setBadge(status, risk.status, {
            open: ['bg-blue-100', 'text-blue-700'],
            closed: ['bg-green-100', 'text-green-700']
        });

        const modal = document.getElementById('risk-detail-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRiskDetail() {
        const modal = document.getElementById('risk-detail-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('risk-detail-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRiskDetail();
        }
    });

    function setBadge(element, value, colors) {
        value = value ?? '-';

        element.innerText = value.charAt(0).toUpperCase() + value.slice(1);

        element.className =
            "inline-flex px-3 py-1 rounded-full text-xs font-semibold";

        if (colors[value]) {
            element.classList.add(...colors[value]);
        } else {
            element.classList.add('bg-slate-100', 'text-slate-700');
        }
    }
</script>
