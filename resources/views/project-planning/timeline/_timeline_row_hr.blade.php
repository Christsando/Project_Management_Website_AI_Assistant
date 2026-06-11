<tr class="hover:bg-slate-50/30 transition">

    <td class="px-6 py-4">
        <div class="flex items-center min-w-0" style="padding-left: {{ $depth * 24 }}px">
            @if ($depth > 0)
                <span class="text-slate-300 mr-2.5 font-mono shrink-0 select-none">↳</span>
            @endif
            <div class="truncate">
                <span class="font-extrabold text-slate-800 text-xs md:text-sm block" title="{{ $wbs->title }}">{{ $wbs->title }}</span>
                <span class="flex flex-col text-[9.5px] text-slate-400 block font-bold uppercase mt-0.5 tracking-wider">ID TUGAS: #{{ $wbs->id }}</span>

                @php
                    $assignedMembers = $hrItems->where('wbs_item_id', $wbs->id);
                    $names = $assignedMembers->map(function ($item) {
                        return $item->teamMember->name;
                    })->implode(', ');
                @endphp

                <span class="text-xs text-slate-400">
                    PIC: {{ $names ?: '-' }}
                </span>
            </div>
        </div>
    </td>

    <td class="px-6 py-4">
        @if ($wbs->timelineItem)
            <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-days text-slate-400"></i>
                {{ $wbs->timelineItem->start_date->format('d M Y') }} s/d
                {{ $wbs->timelineItem->end_date->format('d M Y') }}
            </div>
            @if ($wbs->timelineItem->dependencyWbsItem)
                <div
                    class="text-[9px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/60 inline-flex items-center mt-1 font-extrabold uppercase tracking-wide">
                    <i class="fas fa-link mr-1"></i>
                    Predecessor: #{{ $wbs->timelineItem->dependency_wbs_item_id }}
                    ({{ Str::limit($wbs->timelineItem->dependencyWbsItem->title, 20) }})
                </div>
            @endif
        @else
            <span
                class="text-[9px] text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-200/50 inline-flex items-center font-extrabold uppercase tracking-wide">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ __('Belum dijadwalkan') }}
            </span>
        @endif
    </td>

    <td class="px-6 py-4 font-black text-slate-600 text-xs">
        @if ($wbs->timelineItem)
            {{ $wbs->timelineItem->duration_days }} {{ __('Hari') }}
        @else
            -
        @endif
    </td>

    <td class="px-6 py-4">
        @if ($wbs->timelineItem && $wbs->timelineItem->is_milestone)
            <span
                class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wide bg-indigo-50 text-indigo-700 border border-indigo-200">
                <i class="fas fa-flag text-[9px]"></i>
                {{ $wbs->timelineItem->milestone_name }}
            </span>
        @else
            <span class="text-slate-400 italic text-xs font-semibold">-</span>
        @endif
    </td>

    <td class="px-6 py-4 text-right">
        @php
            $isAssigned = $wbs->isFullyAssigned($hrItems);
        @endphp
        @if (!$isAssigned)
            <button onclick="openAssignModal({{ $wbs->id }})"
                class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg">
                Assign
            </button>
        @else
            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg font-bold">
                Finalized
            </span>
        @endif
    </td>
</tr>

@foreach ($wbs->children as $child)
    @include('project-planning.timeline._timeline_row_hr', ['wbs' => $child, 'depth' => $depth + 1])
@endforeach

<!-- modal -->
<div id="assign-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-[400px]">
        <form id="assign-form" method="POST">
            @csrf
            <div id="member-container" class="space-y-3">
                <!-- MEMBER BLOCK -->
                <div class="member-item border p-3 rounded-lg space-y-2">
                    <div class="flex gap-2 items-center">
                        <select name="team_member_ids[]" class="w-full border rounded p-2">
                            @foreach ($teamMembers as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->name }} - {{ $member->role_name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" onclick="removeMember(this)" class="text-red-500 text-xs font-bold">
                            ✕
                        </button>
                    </div>

                    <!-- WORKLOAD -->
                    <input type="number" name="workloads[]" placeholder="Workload (%)"
                        class="w-full border rounded p-2 text-xs" min="0" max="100">
                </div>
            </div>

            <button type="button" onclick="addMemberDropdown()" class="mt-2 text-xs text-blue-600">
                + Tambah Member
            </button>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeAssignModal()">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentWbsId = null;

    document.getElementById('assign-form').addEventListener('submit', function(e) {
        const workloads = document.querySelectorAll('input[name="workloads[]"]');
        let total = 0;

        workloads.forEach(w => total += Number(w.value || 0));

        if (total > 90) {
            e.preventDefault();
            alert('Total workload tidak boleh lebih dari 90%');
        }
    });

    function openAssignModal(wbsId) {
        currentWbsId = wbsId;
        const form = document.getElementById('assign-form');
        form.action = `/projects/{{ $project->id }}/assign/${wbsId}`;
        document.getElementById('assign-modal').classList.remove('hidden');
    }

    function closeAssignModal() {
        document.getElementById('assign-modal').classList.add('hidden');
    }

    function removeMember(btn) {
        const container = document.getElementById('member-container');

        if (container.children.length > 1) {
            btn.closest('.member-item').remove();
        } else {
            alert('Minimal 1 member harus dipilih');
        }
    }

    function addMemberDropdown() {
        const container = document.getElementById('member-container');

        const wrapper = document.createElement('div');
        wrapper.className = "member-item border p-3 rounded-lg space-y-2";

        // === ROW SELECT + REMOVE ===
        const row = document.createElement('div');
        row.className = "flex gap-2 items-center";

        const select = document.createElement('select');
        select.name = "team_member_ids[]";
        select.className = "w-full border rounded p-2";

        @foreach ($teamMembers as $member)
            const opt{{ $member->id }} = document.createElement('option');
            opt{{ $member->id }}.value = "{{ $member->id }}";
            opt{{ $member->id }}.text = "{{ $member->name }} - {{ $member->role_name }}";
            select.appendChild(opt{{ $member->id }});
        @endforeach

        const removeBtn = document.createElement('button');
        removeBtn.type = "button";
        removeBtn.innerText = "✕";
        removeBtn.className = "text-red-500 text-xs font-bold";
        removeBtn.onclick = function() {
            removeMember(removeBtn);
        };

        row.appendChild(select);
        row.appendChild(removeBtn);

        // === WORKLOAD INPUT ===
        const workload = document.createElement('input');
        workload.type = "number";
        workload.name = "workloads[]";
        workload.placeholder = "Workload (%)";
        workload.className = "w-full border rounded p-2 text-xs";
        workload.min = 0;
        workload.max = 100;

        // === APPEND ===
        wrapper.appendChild(row);
        wrapper.appendChild(workload);

        container.appendChild(wrapper);
    }
</script>
