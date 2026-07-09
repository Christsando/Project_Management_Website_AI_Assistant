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
