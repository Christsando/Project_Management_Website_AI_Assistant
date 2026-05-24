<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-cardSection rounded-xl p-6 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.human-resource.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Kelola Perencanaan SDM (HR Plan)') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-gray-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>
                    @if($hrItems->count() > 0)
                        <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi perencanaan SDM ini? Setelah finalized, seluruh alokasi SDM dan tugas akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Perencanaan SDM') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: HR Summary Column -->
                <div class="space-y-6">
                    <!-- Summaries Cards -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/15 grid grid-cols-3 gap-2">
                        <div class="text-center">
                            <span class="text-blue-100 text-[10px] font-semibold uppercase tracking-wider block">{{ __('Total SDM') }}</span>
                            <span class="text-2xl font-extrabold block mt-1">{{ $totalResources }}</span>
                        </div>
                        <div class="text-center border-l border-white/20">
                            <span class="text-blue-100 text-[10px] font-semibold uppercase tracking-wider block">{{ __('Peran') }}</span>
                            <span class="text-2xl font-extrabold block mt-1">{{ $roleCount }}</span>
                        </div>
                        <div class="text-center border-l border-white/20">
                            <span class="text-blue-100 text-[10px] font-semibold uppercase tracking-wider block">{{ __('PIC') }}</span>
                            <span class="text-2xl font-extrabold block mt-1">{{ $picCount }}</span>
                        </div>
                    </div>

                    <!-- PIC & Workload Table Summary -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-primaryText mb-3 flex items-center gap-2">
                            <i class="fas fa-user-tag text-primary"></i>
                            {{ __('Daftar PIC & Beban Kerja') }}
                        </h4>
                        @php
                            $pics = $hrItems->whereNotNull('person_in_charge')->where('person_in_charge', '!=', '')->groupBy('person_in_charge');
                        @endphp
                        @if($pics->isEmpty())
                            <p class="text-xs text-gray-400 italic text-center py-4">{{ __('Belum ada PIC yang dialokasikan.') }}</p>
                        @else
                            <div class="space-y-3.5 max-h-60 overflow-y-auto pr-1">
                                @foreach($pics as $name => $items)
                                    @php
                                        $totalWorkload = $items->sum('workload_percentage');
                                        $rolesList = $items->pluck('role_name')->unique()->implode(', ');
                                    @endphp
                                    <div class="border-b border-gray-100 pb-2.5 last:border-0 last:pb-0">
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span class="text-primaryText font-bold">{{ $name }}</span>
                                            <span class="font-mono text-secondaryText">{{ $totalWorkload }}% Workload</span>
                                        </div>
                                        <div class="text-[10px] text-secondaryText truncate" title="{{ $rolesList }}">
                                            {{ __('Peran: ') . $rolesList }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- General Settings / Notes -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-primaryText mb-3 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-primary"></i>
                            {{ __('Catatan HR Plan') }}
                        </h4>
                        <form action="{{ route('projects.human-resource.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-primary focus:ring focus:ring-primary/20 mb-3" placeholder="Masukkan catatan perencanaan SDM... ">{{ old('notes', $hrPlan->notes) }}</textarea>
                            <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                {{ __('Simpan Catatan') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Resource Items Management Dashboard -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                        <!-- Section Title -->
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                            <div>
                                <h4 class="font-bold text-base text-primaryText">{{ __('Rincian Kebutuhan SDM') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Kebutuhan peran, kompetensi, jobdesk, PIC, dan beban kerja.') }}</p>
                            </div>
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition gap-1">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambah Peran') }}
                            </button>
                        </div>

                        <!-- Resource Items List -->
                        @if($hrItems->isEmpty())
                            <div class="p-12 text-center">
                                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Alokasi SDM Kosong') }}</h5>
                                <p class="text-xs text-secondaryText mb-4">{{ __('Belum ada kebutuhan tim pelaksana yang diisi.') }}</p>
                                <button type="button" onclick="openAddModal()" class="inline-flex items-center px-3.5 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl text-xs font-semibold hover:bg-blue-600 hover:text-white transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambahkan Tim Pertama') }}
                                </button>
                            </div>
                        @else
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle px-6">
                                    <table class="min-w-full text-left divide-y divide-gray-100">
                                        <thead>
                                            <tr class="text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                                <th class="py-3">{{ __('Peran & Kebutuhan') }}</th>
                                                <th class="py-3">{{ __('Tugas WBS') }}</th>
                                                <th class="py-3 text-center">{{ __('PIC / Beban') }}</th>
                                                <th class="py-3 text-right">{{ __('Durasi / Qty') }}</th>
                                                <th class="py-3 text-right">{{ __('Aksi') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-xs">
                                            @foreach($hrItems as $item)
                                                <tr class="hover:bg-gray-50/50 transition">
                                                    <td class="py-3.5 pr-3 max-w-[200px]">
                                                        <div class="font-bold text-primaryText text-sm">{{ $item->role_name }}</div>
                                                        <div class="text-[10px] text-secondaryText mt-0.5"><span class="font-bold">Skill:</span> {{ $item->required_skill }}</div>
                                                        <div class="text-[10px] text-gray-500 mt-1 line-clamp-2" title="{{ $item->job_description }}">{{ $item->job_description }}</div>
                                                    </td>
                                                    <td class="py-3.5 px-3 max-w-[150px]">
                                                        @if($item->wbsItem)
                                                            <div class="font-semibold text-primaryText truncate" title="{{ $item->wbsItem->title }}">
                                                                {{ $item->wbsItem->title }}
                                                            </div>
                                                            <div class="text-[9px] text-gray-400 font-mono">WBS ID: #{{ $item->wbs_item_id }}</div>
                                                        @else
                                                            <span class="text-gray-400 italic text-[10px]">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3.5 px-3 text-center">
                                                        @if($item->person_in_charge)
                                                            <span class="font-bold text-primaryText block">{{ $item->person_in_charge }}</span>
                                                            @if($item->workload_percentage !== null)
                                                                <span class="text-[9px] px-1.5 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-full font-mono font-bold mt-1 inline-block">
                                                                    {{ $item->workload_percentage }}% Load
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200/50 text-[10px] font-semibold inline-block">
                                                                {{ __('Belum Ada PIC') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3.5 px-3 text-right">
                                                        <span class="font-bold text-primaryText block">{{ $item->quantity }} {{ __('Orang') }}</span>
                                                        @if($item->estimated_work_days)
                                                            <span class="text-[10px] text-secondaryText block mt-0.5 font-mono">{{ $item->estimated_work_days }} {{ __('Hari') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3.5 pl-3 text-right">
                                                        <div class="inline-flex gap-1.5">
                                                            <!-- Edit Button -->
                                                            <button type="button" 
                                                                    onclick='openEditModal({!! json_encode($item) !!})' 
                                                                    class="p-1.5 text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-600 hover:text-white transition"
                                                                    title="{{ __('Edit Item') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <!-- Delete Button -->
                                                            <form action="{{ route('projects.human-resource.items.delete', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item perencanaan SDM ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="p-1.5 text-rose-600 bg-rose-50 border border-rose-300 rounded-lg hover:bg-rose-600 hover:text-white transition"
                                                                        title="{{ __('Hapus Item') }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD HR ITEM -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeAddModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.human-resource.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-plus text-primary"></i>
                                {{ __('Tambah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="add_role_name" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="add_role_name" required value="{{ old('role_name') }}" placeholder="Contoh: Senior UI Designer, Lead Engineer" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="add_required_skill" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="add_required_skill" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Contoh: Figma, CSS, React, REST API... ">{{ old('required_skill') }}</textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="add_job_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="add_job_description" required rows="3" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Jelaskan peran tugas pekerjaan utama peran ini dalam proyek... ">{{ old('job_description') }}</textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="add_wbs_item_id" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="add_wbs_item_id" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}" {{ old('wbs_item_id') == $wbs->id ? 'selected' : '' }}>{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Manual Text) -->
                            <div>
                                <label for="add_person_in_charge" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Nama PIC (Person In Charge) (Optional)') }}</label>
                                <input type="text" name="person_in_charge" id="add_person_in_charge" value="{{ old('person_in_charge') }}" placeholder="Contoh: Christsando, Abid, dsb." class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Workload, Work Days, Quantity (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="add_workload_percentage" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="add_workload_percentage" min="0" max="100" value="{{ old('workload_percentage') }}" placeholder="100" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="add_estimated_work_days" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="add_estimated_work_days" min="1" value="{{ old('estimated_work_days') }}" placeholder="10" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="add_quantity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Jumlah orang">{{ __('Qty') }}</label>
                                    <input type="number" name="quantity" id="add_quantity" required min="1" value="{{ old('quantity', 1) }}" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Keterangan tambahan... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            {{ __('Simpan Peran') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT HR ITEM -->
    <div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeEditModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="edit_role_name" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="edit_role_name" required placeholder="Contoh: Senior UI Designer" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="edit_required_skill" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="edit_required_skill" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Keahlian... "></textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="edit_job_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="edit_job_description" required rows="3" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Jobdesk... "></textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="edit_wbs_item_id" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="edit_wbs_item_id" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Manual Text) -->
                            <div>
                                <label for="edit_person_in_charge" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Nama PIC (Person In Charge) (Optional)') }}</label>
                                <input type="text" name="person_in_charge" id="edit_person_in_charge" placeholder="Contoh: Christsando, Abid, dsb." class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Workload, Work Days, Quantity (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="edit_workload_percentage" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="edit_workload_percentage" min="0" max="100" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="edit_estimated_work_days" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="edit_estimated_work_days" min="1" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="edit_quantity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1" title="Jumlah orang">{{ __('Qty') }}</label>
                                    <input type="number" name="quantity" id="edit_quantity" required min="1" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Catatan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VANILLA JS MODALS TOGGLER -->
    <script>
        function openAddModal() {
            const modal = document.getElementById('add-modal');
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
            document.getElementById('edit_person_in_charge').value = item.person_in_charge || '';
            document.getElementById('edit_workload_percentage').value = item.workload_percentage !== null ? item.workload_percentage : '';
            document.getElementById('edit_estimated_work_days').value = item.estimated_work_days !== null ? item.estimated_work_days : '';
            document.getElementById('edit_quantity').value = item.quantity;
            document.getElementById('edit_notes').value = item.notes || '';

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
