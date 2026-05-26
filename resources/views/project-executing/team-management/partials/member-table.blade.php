<!-- Main Filter & Search Workspace Card -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <!-- Filters Segment -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                </span>
                <input type="text" id="member-search" oninput="filterMembers()" placeholder="Cari anggota tim..."
                    class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 rounded-xl text-xs font-semibold text-slate-700 placeholder-slate-400">
            </div>

            <select id="role-filter" onchange="filterMembers()"
                class="text-xs font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                <option value="">Semua Peran</option>
                <option value="Project Lead">Project Manager</option>
                <option value="Manager">Manager</option>
                <option value="Project Manager Officer">Project Manager Officer</option>
                <option value="TeamIT">Team IT</option>
            </select>

            <select id="status-filter" onchange="filterMembers()"
                class="text-xs font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                <option value="">Status Tersedia</option>
                <option value="High">High</option>
                <option value="Optimal">Optimal</option>
                <option value="Available">Available</option>
            </select>
        </div>
        <div class="text-xs font-bold text-slate-400">
            Menampilkan <span id="filtered-count" class="text-slate-700">4</span> dari <span
                class="text-slate-700">{{ $totalUser }}</span> anggota
        </div>
    </div>

    <!-- Member list table -->
    <div class="overflow-x-auto -mx-6">
        <div class="inline-block min-w-full align-middle px-6">
            <table class="min-w-full text-left divide-y divide-slate-50">

                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3">{{ __('ANGGOTA') }}</th>
                        <th class="py-3 px-3">{{ __('PERAN') }}</th>
                        <th class="py-3 px-3">{{ __('KEAHLIAN') }}</th>
                        <th class="py-3 px-3">{{ __('WORKLOAD') }}</th>
                        <th class="py-3 text-right pr-4">{{ __('AKSI') }}</th>
                    </tr>
                </thead>

                <tbody id="member-table-body" class="divide-y divide-slate-50 text-xs">
                    @foreach ($userData as $user)
                        <tr class="member-row hover:bg-slate-50/30 transition duration-150">
                            <!-- member name-->
                            <td class="py-4 pr-3" data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}" data-status="Optimal">
                                <div class="flex items-center gap-3">

                                    <!-- name data -->
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <span
                                            class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                    </div>

                                    <!-- email data -->
                                    <div>
                                        <div class="font-extrabold text-slate-800 text-sm">{{ $user->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-semibold">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-[#E0F2FE] text-[#0284C7] border border-[#BAE6FD]">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-3 max-w-[200px]">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Agile</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Strategic
                                        Planning
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: 65%">
                                        </div>
                                    </div>
                                    <span class="font-bold text-slate-500 font-mono text-[10px]">65%</span>
                                    <span class="font-bold text-emerald-600 text-[10px]">Optimal</span>
                                </div>
                            </td>
                            <td class="py-4 text-right pr-4 relative">
                                <div class="inline-block text-left">
                                    <button type="button" onclick="toggleDropdown(this)"
                                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div
                                        class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                                        <a href="#" onclick="mockAction('Ubah Beban Kerja Aisya Putri')"
                                            class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah
                                            Beban Kerja</a>
                                        <a href="#" onclick="mockAction('Alokasikan Proyek Aisya Putri')"
                                            class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan
                                            Proyek</a>
                                        <a href="#" onclick="mockAction('Hapus Anggota Aisya Putri')"
                                            class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus
                                            Anggota</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination footer -->
    <div class="flex items-center justify-between border-t border-slate-100 pt-5 mt-6">
        <button type="button" onclick="mockAction('Halaman Sebelumnya')"
            class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
            {{ __('Sebelumnya') }}
        </button>
        <div class="flex items-center gap-1">
            <button type="button"
                class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded-xl text-xs font-bold shadow-sm">1</button>
            <button type="button" onclick="mockAction('Halaman 2')"
                class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">2</button>
            <button type="button" onclick="mockAction('Halaman 3')"
                class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">3</button>
            <span class="text-slate-400 font-bold px-1 text-xs">...</span>
            <button type="button" onclick="mockAction('Halaman 5')"
                class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">5</button>
        </div>
        <button type="button" onclick="mockAction('Halaman Berikutnya')"
            class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
            {{ __('Berikutnya') }}
        </button>
    </div>
</div>