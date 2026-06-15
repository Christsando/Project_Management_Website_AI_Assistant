@props([
    'projects' => collect(),
    'tasks' => collect(),
    'mode' => 'dashboard',
])
<div class="relative w-80">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
        <i class="fas fa-search text-slate-400 text-sm"></i>
    </span>

    <input type="text" id="project-search"
        placeholder="Cari proyek..."
        class="w-full pl-9 pr-4 py-2 bg-slate-100/60 border border-slate-200/50 rounded-full text-xs text-slate-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-400">

    {{-- DROPDOWN RESULT --}}
    <div id="project-dropdown"
        class="absolute top-full mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg hidden max-h-60 overflow-y-auto z-50">
    </div>
</div>

<script>
    const mode = @json($mode);
    const projects = @json($projects ?? []);

    const input = document.getElementById('project-search');
    const dropdown = document.getElementById('project-dropdown');

    input.addEventListener('input', function () {
        const keyword = this.value.toLowerCase();

        const filtered = projects.filter(p =>
            p.title.toLowerCase().includes(keyword)
        );

        renderDropdown(filtered);
    });

    input.addEventListener('focus', function () {
        renderDropdown(projects);
    });

    function renderDropdown(list) {
        dropdown.innerHTML = '';

        if (list.length === 0) {
            dropdown.innerHTML = `<div class="px-3 py-2 text-xs text-slate-400">Tidak ditemukan</div>`;
        } else {
            list.forEach(p => {
                const item = document.createElement('div');
                item.className = "px-3 py-2 text-xs hover:bg-slate-100 cursor-pointer";
                item.innerText = p.title;

                item.onclick = () => selectProject(p);

                dropdown.appendChild(item);
            });
        }

        dropdown.classList.remove('hidden');
    }

    function selectProject(project) {
        input.value = project.title;
        dropdown.classList.add('hidden');

        if (mode === 'dashboard') {
            window.location.href = `/dashboard/${project.id}`;

        } else if (mode === 'task') {
            window.location.href = `/task-management/${project.id}`;
        }
    }

    // klik luar → tutup dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#project-search') && !e.target.closest('#project-dropdown')) {
            dropdown.classList.add('hidden');
        }
    });
</script>