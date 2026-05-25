<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        if (!function_exists('getInitials')) {
            function getInitials($name) {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
                return $initials ?: 'TM';
            }
        }
    @endphp

    <div class="pl-4 pt-2 pb-12">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight flex items-center gap-2">
                        {{ __('Manajemen Tim') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Kelola kolaborator, peran, dan beban kerja tim Anda secara real-time.') }}
                    </p>
                </div>
                <div>
                    <button type="button" onclick="openAddMemberModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-2">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Tambah Anggota') }}
                    </button>
                </div>
            </div>

            <!-- Custom Mock Alerts Container -->
            <div id="mock-alert" class="hidden mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center justify-between shadow-sm transition-all">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span id="mock-alert-text" class="font-semibold"></span>
                </div>
                <button onclick="document.getElementById('mock-alert').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Summary Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card 1: Total Anggota -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center justify-between min-h-[110px] relative overflow-hidden">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                            {{ __('Total Anggota') }}
                        </span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                            24
                        </h3>
                        <div class="mt-2 text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            <span>{{ __('+3 bulan ini') }}</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-sm shadow-sm border border-blue-100">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>

                <!-- Card 2: Beban Rata-rata -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center justify-between min-h-[110px] relative overflow-hidden">
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                            {{ __('Beban Rata-rata') }}
                        </span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                            78%
                        </h3>
                        <div class="mt-2 text-[10px] font-bold text-amber-600 flex items-center gap-1">
                            <span>{{ __('Status: Produktif') }}</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-sm shadow-sm border border-purple-100">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>

                <!-- Card 3: Alokasi Tim Proyek -->
                <div class="bg-blue-600 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[110px] relative overflow-hidden text-white">
                    <div>
                        <span class="text-blue-100 text-[10px] font-bold uppercase tracking-wider block">
                            {{ __('Alokasi Tim Proyek') }}
                        </span>
                        <p class="text-[10px] text-blue-100 mt-1 font-semibold leading-relaxed">
                            {{ __('Distribusi anggota pada 5 proyek aktif yang sedang berjalan saat ini.') }}
                        </p>
                    </div>
                    <!-- Overlapping avatars -->
                    <div class="flex items-center -space-x-2 mt-4">
                        <div class="w-7 h-7 rounded-full bg-slate-200 border-2 border-blue-600 flex items-center justify-center font-bold text-[9px] text-slate-700 shadow-sm shrink-0">LM</div>
                        <div class="w-7 h-7 rounded-full bg-slate-300 border-2 border-blue-600 flex items-center justify-center font-bold text-[9px] text-slate-700 shadow-sm shrink-0">AD</div>
                        <div class="w-7 h-7 rounded-full bg-slate-400 border-2 border-blue-600 flex items-center justify-center font-bold text-[9px] text-slate-700 shadow-sm shrink-0">BP</div>
                        <div class="w-7 h-7 rounded-full bg-slate-800 border-2 border-blue-600 flex items-center justify-center font-bold text-[8px] text-white shadow-sm shrink-0 font-mono">+21</div>
                    </div>
                </div>
            </div>

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
                        
                        <select id="role-filter" onchange="filterMembers()" class="text-xs font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            <option value="">Semua Peran</option>
                            <option value="Project Lead">Project Lead</option>
                            <option value="Backend Eng">Backend Eng</option>
                            <option value="UI/UX Designer">UI/UX Designer</option>
                            <option value="Analyst">Analyst</option>
                        </select>
                        
                        <select id="status-filter" onchange="filterMembers()" class="text-xs font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            <option value="">Status Tersedia</option>
                            <option value="Optimal">Optimal</option>
                            <option value="High">High</option>
                            <option value="Available">Available</option>
                        </select>
                    </div>
                    <div class="text-xs font-bold text-slate-400">
                        Menampilkan <span id="filtered-count" class="text-slate-700">4</span> dari <span class="text-slate-700">24</span> anggota
                    </div>
                </div>

                <!-- Table -->
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
                                <!-- Row 1: Aisya Putri -->
                                <tr class="member-row hover:bg-slate-50/30 transition duration-150" data-name="Aisya Putri" data-email="aisya.p@kelolain.com" data-role="Project Lead" data-status="Optimal">
                                    <td class="py-4 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                                    AP
                                                </div>
                                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-800 text-sm">Aisya Putri</div>
                                                <div class="text-[10px] text-slate-400 font-semibold">aisya.p@kelolain.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-[#E0F2FE] text-[#0284C7] border border-[#BAE6FD]">
                                            Project Lead
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 max-w-[200px]">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Agile</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Strategic Planning</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                                <div class="h-full rounded-full bg-emerald-500" style="width: 65%"></div>
                                            </div>
                                            <span class="font-bold text-slate-500 font-mono text-[10px]">65%</span>
                                            <span class="font-bold text-emerald-600 text-[10px]">Optimal</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right pr-4 relative">
                                        <div class="inline-block text-left">
                                            <button type="button" onclick="toggleDropdown(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                                                <i class="fas fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <!-- Dropdown menu -->
                                            <div class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                                                <a href="#" onclick="mockAction('Ubah Beban Kerja Aisya Putri')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah Beban Kerja</a>
                                                <a href="#" onclick="mockAction('Alokasikan Proyek Aisya Putri')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan Proyek</a>
                                                <a href="#" onclick="mockAction('Hapus Anggota Aisya Putri')" class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus Anggota</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Row 2: Budi Raharjo -->
                                <tr class="member-row hover:bg-slate-50/30 transition duration-150" data-name="Budi Raharjo" data-email="budi.r@kelolain.com" data-role="Backend Eng" data-status="High">
                                    <td class="py-4 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                                    BR
                                                </div>
                                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-800 text-sm">Budi Raharjo</div>
                                                <div class="text-[10px] text-slate-400 font-semibold">budi.r@kelolain.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-[#F3E8FF] text-[#7E22CE] border border-[#E9D5FF]">
                                            Backend Eng
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 max-w-[200px]">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Node.js</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Postgres</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                                <div class="h-full rounded-full bg-rose-500" style="width: 90%"></div>
                                            </div>
                                            <span class="font-bold text-slate-500 font-mono text-[10px]">90%</span>
                                            <span class="font-bold text-rose-600 text-[10px]">High</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right pr-4 relative">
                                        <div class="inline-block text-left">
                                            <button type="button" onclick="toggleDropdown(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                                                <i class="fas fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <!-- Dropdown menu -->
                                            <div class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                                                <a href="#" onclick="mockAction('Ubah Beban Kerja Budi Raharjo')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah Beban Kerja</a>
                                                <a href="#" onclick="mockAction('Alokasikan Proyek Budi Raharjo')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan Proyek</a>
                                                <a href="#" onclick="mockAction('Hapus Anggota Budi Raharjo')" class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus Anggota</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Row 3: Citra Dewi -->
                                <tr class="member-row hover:bg-slate-50/30 transition duration-150" data-name="Citra Dewi" data-email="citra.d@kelolain.com" data-role="UI/UX Designer" data-status="Available">
                                    <td class="py-4 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                                    CD
                                                </div>
                                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-800 text-sm">Citra Dewi</div>
                                                <div class="text-[10px] text-slate-400 font-semibold">citra.d@kelolain.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
                                            UI/UX Designer
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 max-w-[200px]">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Figma</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Prototyping</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                                <div class="h-full rounded-full bg-blue-500" style="width: 30%"></div>
                                            </div>
                                            <span class="font-bold text-slate-500 font-mono text-[10px]">30%</span>
                                            <span class="font-bold text-blue-600 text-[10px]">Available</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right pr-4 relative">
                                        <div class="inline-block text-left">
                                            <button type="button" onclick="toggleDropdown(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                                                <i class="fas fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <!-- Dropdown menu -->
                                            <div class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                                                <a href="#" onclick="mockAction('Ubah Beban Kerja Citra Dewi')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah Beban Kerja</a>
                                                <a href="#" onclick="mockAction('Alokasikan Proyek Citra Dewi')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan Proyek</a>
                                                <a href="#" onclick="mockAction('Hapus Anggota Citra Dewi')" class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus Anggota</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Row 4: Deni Kurniawan -->
                                <tr class="member-row hover:bg-slate-50/30 transition duration-150" data-name="Deni Kurniawan" data-email="deni.k@kelolain.com" data-role="Analyst" data-status="Optimal">
                                    <td class="py-4 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                                    DK
                                                </div>
                                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-800 text-sm">Deni Kurniawan</div>
                                                <div class="text-[10px] text-slate-400 font-semibold">deni.k@kelolain.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Analyst
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 max-w-[200px]">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">SQL</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">Tableau</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                                                <div class="h-full rounded-full bg-emerald-500" style="width: 50%"></div>
                                            </div>
                                            <span class="font-bold text-slate-500 font-mono text-[10px]">50%</span>
                                            <span class="font-bold text-emerald-600 text-[10px]">Optimal</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right pr-4 relative">
                                        <div class="inline-block text-left">
                                            <button type="button" onclick="toggleDropdown(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                                                <i class="fas fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <!-- Dropdown menu -->
                                            <div class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                                                <a href="#" onclick="mockAction('Ubah Beban Kerja Deni Kurniawan')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah Beban Kerja</a>
                                                <a href="#" onclick="mockAction('Alokasikan Proyek Deni Kurniawan')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan Proyek</a>
                                                <a href="#" onclick="mockAction('Hapus Anggota Deni Kurniawan')" class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus Anggota</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination footer -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-5 mt-6">
                    <button type="button" onclick="mockAction('Halaman Sebelumnya')" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
                        {{ __('Sebelumnya') }}
                    </button>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded-xl text-xs font-bold shadow-sm">1</button>
                        <button type="button" onclick="mockAction('Halaman 2')" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">2</button>
                        <button type="button" onclick="mockAction('Halaman 3')" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">3</button>
                        <span class="text-slate-400 font-bold px-1 text-xs">...</span>
                        <button type="button" onclick="mockAction('Halaman 5')" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 text-slate-500 rounded-xl text-xs font-bold">5</button>
                    </div>
                    <button type="button" onclick="mockAction('Halaman Berikutnya')" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
                        {{ __('Berikutnya') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MOCK MODAL: ADD MEMBER -->
    <div id="add-member-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true" onclick="closeAddMemberModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form onsubmit="saveMockMember(event)">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-user-plus text-blue-600"></i>
                                {{ __('Tambah Anggota Tim') }}
                            </h3>
                            <button type="button" onclick="closeAddMemberModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="member_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Nama Lengkap') }}</label>
                                <input type="text" id="member_name" required placeholder="Contoh: John Doe" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="member_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Alamat Email') }}</label>
                                <input type="email" id="member_email" required placeholder="Contoh: john@kelolain.com" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Peran & Status (Grid) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="member_role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Peran') }}</label>
                                    <select id="member_role" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="Project Lead">Project Lead</option>
                                        <option value="Backend Eng">Backend Eng</option>
                                        <option value="UI/UX Designer">UI/UX Designer</option>
                                        <option value="Analyst">Analyst</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="member_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Beban Kerja (Status)') }}</label>
                                    <select id="member_status" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="Optimal">Optimal (50-70%)</option>
                                        <option value="High">High (>80%)</option>
                                        <option value="Available">Available (<40%)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Keahlian -->
                            <div>
                                <label for="member_skills" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keahlian (Pisahkan dengan koma)') }}</label>
                                <input type="text" id="member_skills" placeholder="Contoh: PHP, Laravel, MySQL" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            {{ __('Tambah') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS script for filters and dropdown menus -->
    <script>
        function toggleDropdown(button) {
            // Close all other open dropdowns first
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.add('hidden');
                }
            });
            // Toggle clicked dropdown
            button.nextElementSibling.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.relative') || !e.target.closest('.fas.fa-ellipsis-v')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        function filterMembers() {
            const search = document.getElementById('member-search').value.toLowerCase();
            const role = document.getElementById('role-filter').value;
            const status = document.getElementById('status-filter').value;
            
            const rows = document.querySelectorAll('.member-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const rowRole = row.getAttribute('data-role');
                const rowStatus = row.getAttribute('data-status');
                
                const matchesSearch = name.includes(search) || email.includes(search);
                const matchesRole = !role || rowRole === role;
                const matchesStatus = !status || rowStatus === status;
                
                if (matchesSearch && matchesRole && matchesStatus) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            document.getElementById('filtered-count').innerText = visibleCount;
        }

        function openAddMemberModal() {
            document.getElementById('add-member-modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeAddMemberModal() {
            document.getElementById('add-member-modal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function saveMockMember(e) {
            e.preventDefault();
            const name = document.getElementById('member_name').value;
            const email = document.getElementById('member_email').value;
            const role = document.getElementById('member_role').value;
            const status = document.getElementById('member_status').value;
            const skillsInput = document.getElementById('member_skills').value;
            
            // Map status properties
            let workload = '60%';
            let loadBar = 'bg-emerald-500 animate-all';
            let loadLabelColor = 'text-emerald-600';
            if (status === 'High') {
                workload = '85%';
                loadBar = 'bg-rose-500';
                loadLabelColor = 'text-rose-600';
            } else if (status === 'Available') {
                workload = '35%';
                loadBar = 'bg-blue-500';
                loadLabelColor = 'text-blue-600';
            }

            // Map role badges
            let roleBadge = 'bg-slate-100 text-slate-700 border border-slate-200';
            if (role === 'Project Lead') {
                roleBadge = 'bg-[#E0F2FE] text-[#0284C7] border border-[#BAE6FD]';
            } else if (role === 'Backend Eng') {
                roleBadge = 'bg-[#F3E8FF] text-[#7E22CE] border border-[#E9D5FF]';
            } else if (role === 'UI/UX Designer') {
                roleBadge = 'bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]';
            }

            // Generate initials
            const initials = name.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase() || 'TM';

            // Split skills
            const skills = skillsInput.split(',').map(s => s.trim()).filter(s => s.length > 0);
            let skillsHTML = '';
            skills.forEach(s => {
                skillsHTML += `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border bg-slate-50 text-slate-600 border-slate-200">${s}</span>`;
            });

            // Append row to table
            const tbody = document.getElementById('member-table-body');
            const newRow = document.createElement('tr');
            newRow.className = 'member-row hover:bg-slate-50/30 transition duration-150';
            newRow.setAttribute('data-name', name);
            newRow.setAttribute('data-email', email);
            newRow.setAttribute('data-role', role);
            newRow.setAttribute('data-status', status);

            newRow.innerHTML = `
                <td class="py-4 pr-3">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-sm border border-blue-100">
                                ${initials}
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-800 text-sm">${name}</div>
                            <div class="text-[10px] text-slate-400 font-semibold">${email}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold ${roleBadge}">
                        ${role}
                    </span>
                </td>
                <td class="py-4 px-3 max-w-[200px]">
                    <div class="flex flex-wrap gap-1">
                        ${skillsHTML || '<span class="text-slate-400 italic text-[10px]">-</span>'}
                    </div>
                </td>
                <td class="py-4 px-3">
                    <div class="flex items-center gap-3">
                        <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden shrink-0">
                            <div class="h-full rounded-full ${loadBar}" style="width: ${workload}"></div>
                        </div>
                        <span class="font-bold text-slate-500 font-mono text-[10px]">${workload}</span>
                        <span class="font-bold ${loadLabelColor} text-[10px]">${status}</span>
                    </div>
                </td>
                <td class="py-4 text-right pr-4 relative">
                    <div class="inline-block text-left">
                        <button type="button" onclick="toggleDropdown(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
                            <i class="fas fa-ellipsis-v text-xs"></i>
                        </button>
                        <div class="dropdown-menu hidden absolute right-4 mt-1 w-36 bg-white border border-slate-100 rounded-xl shadow-lg z-20 py-1 font-bold text-slate-600 text-[10px] text-left">
                            <a href="#" onclick="mockAction('Ubah Beban Kerja ${name}')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Ubah Beban Kerja</a>
                            <a href="#" onclick="mockAction('Alokasikan Proyek ${name}')" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-800 transition">Alokasikan Proyek</a>
                            <a href="#" onclick="mockAction('Hapus Anggota ${name}')" class="block px-4 py-2 hover:bg-slate-50 hover:text-rose-600 transition border-t border-slate-50">Hapus Anggota</a>
                        </div>
                    </div>
                </td>
            `;

            tbody.appendChild(newRow);

            // Close modal
            closeAddMemberModal();

            // Clear inputs
            document.getElementById('member_name').value = '';
            document.getElementById('member_email').value = '';
            document.getElementById('member_skills').value = '';

            // Show success alert
            document.getElementById('mock-alert-text').innerText = `Anggota tim "${name}" berhasil ditambahkan ke daftar.`;
            document.getElementById('mock-alert').classList.remove('hidden');

            // Trigger filter update
            filterMembers();
        }

        function mockAction(message) {
            alert(`Aksi simulasi: ${message}`);
        }
    </script>
</x-app-layout>