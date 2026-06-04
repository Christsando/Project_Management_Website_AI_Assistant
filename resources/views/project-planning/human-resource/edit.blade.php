<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = $userRole === 'pmo' || $userRole === 'project management officer';
        $isDraft = $hrPlan && $hrPlan->status === 'draft';

        if (!function_exists('getInitials')) {
            function getInitials($name)
            {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                    if (strlen($initials) >= 2) {
                        break;
                    }
                }
                return $initials ?: 'PIC';
            }
        }

        // Calculations for workload average and capacity status
        $avgWorkload = 0;
        $overloadCount = 0;
        $optimalCount = 0;
        $underloadCount = 0;
        if ($hrPlan && $hrItems->count() > 0) {
            $avgWorkload = round($hrItems->avg('workload_percentage') ?: 0);

            $pics = $hrItems
                ->whereNotNull('person_in_charge')
                ->where('person_in_charge', '!=', '')
                ->groupBy('person_in_charge');
            foreach ($pics as $name => $items) {
                $wl = $items->sum('workload_percentage');
                if ($wl > 85) {
                    $overloadCount++;
                } elseif ($wl >= 60) {
                    $optimalCount++;
                } else {
                    $underloadCount++;
                }
            }
        }
    @endphp

    <div class="p-6 bg-white rounded-2xl border-slate-100 border shadow-sm">
        <div class="w-full mx-auto space-y-6">

            <!-- Top Sub-Navigation Tabs (Redesigned as sleek pill tabs) -->
            @include('project-planning.human-resource.partials.sub-navigation')


            <!-- Back Navigation & Header Section -->
            <div class="space-y-4">

                @include('project-planning.human-resource.partials.sub-header', [
                    'breadcrumb' => __('Perencanaan Proyek') . ' > ' . __('Perencanaan SDM'),
                    'title' => __('Kelola Perencanaan SDM (HR Plan)'),
                    'description' => __('Kelola beban kerja personil dan alokasikan peran strategis untuk memastikan keberhasilan proyek tepat waktu.'),
                    'project' => $project,
                    'actionButtonEnabled' => $isPmo && $isDraft,
                ])
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div
                    class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-100 text-rose-850 rounded-xl text-xs shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Finalized / Draft Banner -->
            @include('project-planning.human-resource.partials.finalized-draf-banner')

            <!-- Plan content -->
            @if (!$hrPlan)
                @include('project-planning.human-resource.partials.draf-message')
            @else
                <!-- Metric Cards Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @include('project-planning.human-resource.partials.total-personil-card')
                    @include('project-planning.human-resource.partials.avarage-weight-card')
                    @include('project-planning.human-resource.partials.project-quality-card')
                </div>

                <!-- Main Resource List Table Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    @include('project-planning.human-resource.partials.title-action',[ 'isEditable' => $isEditable ])

                    <!-- Table -->
                    @if ($hrItems->isEmpty())
                        @include('project-planning.human-resource.partials.empty-message',['isEditable' => $isEditable ])
                    @else
                        <div class="overflow-x-auto">
                            @include('project-planning.human-resource.partials.workload-table')
                        </div>

                        <!-- Footer Pagination / Stats -->
                        <div
                            class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500 font-medium">
                            <div id="pagination-stats">
                                {{ __('Menampilkan ') }}<span class="font-bold text-slate-700"
                                    id="visible-count">{{ $hrItems->count() }}</span>{{ __(' dari ') }}<span
                                    class="font-bold text-slate-700">{{ $hrItems->count() }}</span>{{ __(' personil') }}
                            </div>
                            <div class="inline-flex gap-1">
                                <button type="button" disabled
                                    class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Sebelumnya</button>
                                <button type="button"
                                    class="px-3 py-1 border border-slate-800 bg-slate-800 text-white rounded-lg text-[11px] font-bold">1</button>
                                <button type="button" disabled
                                    class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Selanjutnya</button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bottom Section (Two Columns) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Column 1: Skill Distribution -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                            <i class="fas fa-info-circle text-slate-400"></i>
                            <h4 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider">
                                {{ __('Distribusi Keahlian') }}</h4>
                        </div>
                        <div class="space-y-3.5 text-xs font-bold text-slate-750">
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Frontend Development</span>
                                    <span class="font-mono text-slate-800">40%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-700 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Backend Development</span>
                                    <span class="font-mono text-slate-800">35%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-600 rounded-full" style="width: 35%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>UI/UX Design</span>
                                    <span class="font-mono text-slate-800">15%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-500 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>DevOps & Testing</span>
                                    <span class="font-mono text-slate-800">10%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-400 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Prediksi Ketersediaan Tim (CSS Bar Chart) -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <h4
                                class="font-extrabold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                {{ __('Prediksi Ketersediaan Tim') }}
                            </h4>
                            <i class="fas fa-chart-bar text-slate-450"></i>
                        </div>
                        <!-- Pure CSS vertical bars chart matching mockup -->
                        <div
                            class="flex items-end justify-between h-44 pt-4 px-2 font-mono text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-100 rounded-lg h-20 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>JAN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-150 rounded-lg h-24 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>FEB</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-[#0B1329] rounded-lg h-32 shadow-sm transition-all duration-300 hover:bg-slate-800">
                                </div>
                                <span class="text-slate-800 font-black">MAR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-200 rounded-lg h-28 transition-all duration-300 hover:bg-slate-350">
                                </div>
                                <span>APR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-100 rounded-lg h-12 transition-all duration-300 hover:bg-slate-150">
                                </div>
                                <span>MEI</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-150 rounded-lg h-16 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>JUN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-200 rounded-lg h-20 transition-all duration-300 hover:bg-slate-250">
                                </div>
                                <span>JUL</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Form Card (Updating Notes) -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-3">
                    <h4 class="font-extrabold text-xs uppercase text-slate-400 tracking-wider flex items-center gap-2">
                        <i class="fas fa-sticky-note text-[#0B1329]"></i>
                        {{ __('Catatan Perencanaan SDM') }}
                    </h4>
                    <form action="{{ route('projects.human-resource.update', $project->id) }}" method="POST"
                        class="space-y-3">
                        @csrf
                        @method('PUT')
                        <textarea name="notes" rows="3"
                            class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-150 text-slate-700"
                            placeholder="Masukkan catatan perencanaan SDM... ">{{ old('notes', $hrPlan->notes) }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                {{ __('Simpan Catatan') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: ADD HR ITEM (Redesigned visual) -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true"
                onclick="closeAddModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.human-resource.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-plus text-[#0B1329]"></i>
                                {{ __('Tambah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()"
                                class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="add_role_name"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="add_role_name" required
                                    value="{{ old('role_name') }}"
                                    placeholder="Contoh: Senior UI Designer, Lead Engineer"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="add_required_skill"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="add_required_skill" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Contoh: Figma, CSS, React, REST API... ">{{ old('required_skill') }}</textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="add_job_description"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="add_job_description" required rows="3"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Jelaskan peran tugas pekerjaan utama peran ini dalam proyek... ">{{ old('job_description') }}</textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="add_wbs_item_id"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="add_wbs_item_id"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach ($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}"
                                            {{ old('wbs_item_id') == $wbs->id ? 'selected' : '' }}>{{ $wbs->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Team Member Dropdown & Fallback) -->
                            <div>
                                <label for="add_team_member_id"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Pilih Anggota Tim (PIC)') }}</label>
                                <select name="team_member_id" id="add_team_member_id"
                                    onchange="updateTeamMemberInfo(this, 'add')"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Masukkan PIC Manual / Bebas') }} --</option>
                                    @foreach ($teamMembers as $member)
                                        <option value="{{ $member->id }}" data-role="{{ $member->role_name }}"
                                            data-skills="{{ $member->skills }}"
                                            data-workload="{{ $member->current_workload_percentage }}"
                                            data-remaining="{{ $member->remaining_capacity_percentage }}"
                                            data-status="{{ $member->workload_status }}"
                                            {{ old('team_member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Member Info Box -->
                            <div id="add_member_info_box"
                                class="hidden p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-xs space-y-1.5">
                                <div class="flex justify-between font-bold text-slate-700">
                                    <span>Peran: <span id="add_info_role"
                                            class="text-blue-700 font-extrabold"></span></span>
                                    <span>Status: <span id="add_info_status"
                                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold"></span></span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-semibold">
                                    Keahlian: <span id="add_info_skills"></span>
                                </div>
                                <div
                                    class="flex justify-between text-[10px] text-slate-600 font-extrabold border-t border-blue-100/50 pt-1.5">
                                    <span>Current Workload: <span id="add_info_workload"></span>%</span>
                                    <span>Remaining Capacity: <span id="add_info_remaining"></span>%</span>
                                </div>
                            </div>

                            <div id="add_pic_manual_container">
                                <label for="add_person_in_charge"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Manual)') }}</label>
                                <input type="text" name="person_in_charge" id="add_person_in_charge"
                                    value="{{ old('person_in_charge') }}" placeholder="Contoh: John Doe"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Workload & Work Days (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="add_workload_percentage"
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"
                                        title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="add_workload_percentage"
                                        min="0" max="100" value="{{ old('workload_percentage') }}"
                                        placeholder="100"
                                        class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                                <div>
                                    <label for="add_estimated_work_days"
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"
                                        title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="add_estimated_work_days"
                                        min="1" value="{{ old('estimated_work_days') }}" placeholder="10"
                                        class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Keterangan tambahan... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-150 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            {{ __('Simpan Peran') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT HR ITEM (Redesigned visual) -->
    <div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true"
                onclick="closeEditModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()"
                                class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="edit_role_name"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="edit_role_name" required
                                    placeholder="Contoh: Senior UI Designer"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="edit_required_skill"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="edit_required_skill" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Keahlian... "></textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="edit_job_description"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="edit_job_description" required rows="3"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Jobdesk... "></textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="edit_wbs_item_id"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="edit_wbs_item_id"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach ($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Team Member Dropdown & Fallback) -->
                            <div>
                                <label for="edit_team_member_id"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Pilih Anggota Tim (PIC)') }}</label>
                                <select name="team_member_id" id="edit_team_member_id"
                                    onchange="updateTeamMemberInfo(this, 'edit')"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Masukkan PIC Manual / Bebas') }} --</option>
                                    @foreach ($teamMembers as $member)
                                        <option value="{{ $member->id }}" data-role="{{ $member->role_name }}"
                                            data-skills="{{ $member->skills }}"
                                            data-workload="{{ $member->current_workload_percentage }}"
                                            data-remaining="{{ $member->remaining_capacity_percentage }}"
                                            data-status="{{ $member->workload_status }}">
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Member Info Box -->
                            <div id="edit_member_info_box"
                                class="hidden p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-xs space-y-1.5">
                                <div class="flex justify-between font-bold text-slate-700">
                                    <span>Peran: <span id="edit_info_role"
                                            class="text-blue-700 font-extrabold"></span></span>
                                    <span>Status: <span id="edit_info_status"
                                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold"></span></span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-semibold">
                                    Keahlian: <span id="edit_info_skills"></span>
                                </div>
                                <div
                                    class="flex justify-between text-[10px] text-slate-650 font-bold border-t border-blue-100/50 pt-1.5">
                                    <span>Current Workload: <span id="edit_info_workload"></span>%</span>
                                    <span>Remaining Capacity: <span id="edit_info_remaining"></span>%</span>
                                </div>
                            </div>

                            <div id="edit_pic_manual_container">
                                <label for="edit_person_in_charge"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Manual)') }}</label>
                                <input type="text" name="person_in_charge" id="edit_person_in_charge"
                                    placeholder="Contoh: John Doe"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Workload & Work Days (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_workload_percentage"
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"
                                        title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="edit_workload_percentage"
                                        min="0" max="100"
                                        class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                                <div>
                                    <label for="edit_estimated_work_days"
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"
                                        title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="edit_estimated_work_days"
                                        min="1"
                                        class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700"
                                    placeholder="Catatan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-150 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VANILLA JS MODALS TOGGLER -->
    <script>
        function updateTeamMemberInfo(selectEl, prefix) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const infoBox = document.getElementById(prefix + '_member_info_box');
            const manualContainer = document.getElementById(prefix + '_pic_manual_container');
            const manualInput = document.getElementById(prefix + '_person_in_charge');

            if (selectedOpt && selectedOpt.value !== '') {
                const role = selectedOpt.getAttribute('data-role');
                const skills = selectedOpt.getAttribute('data-skills');
                const workload = selectedOpt.getAttribute('data-workload');
                const remaining = selectedOpt.getAttribute('data-remaining');
                const status = selectedOpt.getAttribute('data-status');

                document.getElementById(prefix + '_info_role').innerText = role;
                document.getElementById(prefix + '_info_skills').innerText = skills;

                // For workload and remaining capacity
                const workloadEl = document.getElementById(prefix + '_info_workload');
                if (workloadEl) workloadEl.innerText = workload;
                const remainingEl = document.getElementById(prefix + '_info_remaining');
                if (remainingEl) remainingEl.innerText = remaining;

                const statusEl = document.getElementById(prefix + '_info_status');
                if (statusEl) {
                    statusEl.innerText = status;
                    statusEl.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold ';
                    if (status === 'Full') {
                        statusEl.className += 'bg-rose-100 text-rose-800 border border-rose-200';
                    } else if (status === 'Nearly Full') {
                        statusEl.className += 'bg-amber-100 text-amber-800 border border-amber-200';
                    } else if (status === 'Partially Allocated') {
                        statusEl.className += 'bg-blue-100 text-blue-800 border border-blue-200';
                    } else {
                        statusEl.className += 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                    }
                }

                infoBox.classList.remove('hidden');
                manualContainer.classList.add('hidden');
                if (manualInput) {
                    manualInput.value = '';
                    manualInput.removeAttribute('required');
                }

                // Optionally auto-fill Role and Skills if they are empty
                const roleInput = document.getElementById(prefix + '_role_name');
                const skillsInput = document.getElementById(prefix + '_required_skill');
                if (roleInput && roleInput.value === '') roleInput.value = role;
                if (skillsInput && skillsInput.value === '') skillsInput.value = skills;
            } else {
                infoBox.classList.add('hidden');
                manualContainer.classList.remove('hidden');
            }
        }

        function openAddModal() {
            const modal = document.getElementById('add-modal');
            const selectEl = document.getElementById('add_team_member_id');
            if (selectEl) {
                selectEl.value = '';
                updateTeamMemberInfo(selectEl, 'add');
            }
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditModal(item) {
            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-item-form');

            // Set input values
            document.getElementById('edit_role_name').value = item.role_name;
            document.getElementById('edit_required_skill').value = item.required_skill;
            document.getElementById('edit_job_description').value = item.job_description;
            document.getElementById('edit_wbs_item_id').value = item.wbs_item_id || '';
            document.getElementById('edit_workload_percentage').value = item.workload_percentage !== null ? item
                .workload_percentage : '';
            document.getElementById('edit_estimated_work_days').value = item.estimated_work_days !== null ? item
                .estimated_work_days : '';
            document.getElementById('edit_notes').value = item.notes || '';

            // Set team_member_id dropdown selection
            const selectEl = document.getElementById('edit_team_member_id');
            if (selectEl) {
                selectEl.value = item.team_member_id || '';
                updateTeamMemberInfo(selectEl, 'edit');
            }

            // If manual text PIC was used, restore it
            if (!item.team_member_id) {
                document.getElementById('edit_person_in_charge').value = item.person_in_charge || '';
            }

            // Update form action dynamically
            form.action = `/projects/{{ $project->id }}/human-resource/items/${item.id}`;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</x-app-layout>
