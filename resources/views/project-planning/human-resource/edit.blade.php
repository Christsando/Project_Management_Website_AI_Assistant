<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        $isDraft = $hrPlan && $hrPlan->status === 'draft';
        
        if (!function_exists('getInitials')) {
            function getInitials($name) {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
                return $initials ?: 'PIC';
            }
        }
    @endphp

    <div class="pl-4 pt-2 pb-12">
        <!-- Top Sub-Navigation Tabs -->
        <div class="flex items-center gap-6 border-b border-slate-100 mb-6 px-4">
            <a href="{{ route('projects.human-resource.show', $project->id) }}" class="pb-3 text-xs font-bold text-blue-600 border-b-2 border-blue-600 transition">
                {{ __('Human Resource Planning') }}
            </a>
            <a href="{{ route('projects.timeline.show', $project->id) }}" class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Gantt Chart') }}
            </a>
            <a href="{{ route('projects.budget.show', $project->id) }}" class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Budgeting') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.human-resource.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Kelola Perencanaan SDM (HR Plan)') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Kelola alokasi tim, keahlian, dan beban kerja proyek secara terpusat.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-400 font-medium">
                        <span>{{ __('Proyek:') }}</span>
                        <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $project->title }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-slate-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>
                    @if($hrItems->count() > 0)
                        <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi perencanaan SDM ini? Setelah finalized, seluruh alokasi SDM dan tugas akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Perencanaan SDM') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-semibold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Finalized / Draft Banner -->
            @if($hrPlan && $hrPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Siap digunakan untuk Risk Management') }}</h4>
                            <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Data personil SDM Anda telah divalidasi dan siap diintegrasikan dengan modul manajemen risiko.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Siap digunakan untuk Risk Management') }}</h4>
                            <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Data personil SDM Anda telah divalidasi dan siap diintegrasikan dengan modul manajemen risiko.') }}
                            </p>
                        </div>
                    </div>
                    @if($isPmo && $hrItems->count() > 0)
                        <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi perencanaan SDM ini? Setelah finalized, seluruh alokasi SDM dan tugas akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Finalisasi Perencanaan SDM') }}
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Plan content -->
            @if(!$hrPlan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-slate-800 mb-1">{{ __('Belum Ada Perencanaan SDM') }}</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">{{ __('Perencanaan sumber daya manusia (SDM) proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Main Layout Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Summary and Notes Form -->
                    <div class="space-y-6">
                        <!-- Total SDM Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center justify-between min-h-[110px] relative overflow-hidden">
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                    {{ __('Total SDM Terdaftar') }}
                                </span>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                                    {{ $totalResources }}
                                </h3>
                                <div class="mt-2 text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                    <span>{{ __('+4 orang bulan ini') }}</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-sm shadow-sm border border-blue-100">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>

                        <!-- Peran Unik Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center justify-between min-h-[110px] relative overflow-hidden">
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                    {{ __('Peran Unik') }}
                                </span>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                                    {{ $roleCount }}
                                </h3>
                                <p class="text-[10px] text-slate-400 font-semibold mt-2">
                                    {{ __('Didominasi oleh departemen Design dan Engineering.') }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-sm shadow-sm border border-emerald-100">
                                <i class="fas fa-compass"></i>
                            </div>
                        </div>

                        <!-- PIC & Workload Table Summary -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-5 flex items-center gap-2">
                                <i class="fas fa-user-tag text-blue-600"></i>
                                {{ __('Ringkasan Beban Kerja') }}
                            </h4>
                            @php
                                $pics = $hrItems->whereNotNull('person_in_charge')->where('person_in_charge', '!=', '')->groupBy('person_in_charge');
                            @endphp
                            @if($pics->isEmpty())
                                <p class="text-xs text-slate-400 italic text-center py-4">{{ __('Belum ada PIC yang dialokasikan.') }}</p>
                            @else
                                <div class="space-y-4 max-h-60 overflow-y-auto pr-1">
                                    @foreach($pics as $name => $items)
                                        @php
                                            $totalWorkload = $items->sum('workload_percentage');
                                            
                                            // Progress bar color based on workload percentage
                                            $barColor = 'bg-emerald-500'; // green/emerald for low
                                            if ($totalWorkload > 90) {
                                                $barColor = 'bg-rose-500'; // red for overloaded
                                            } elseif ($totalWorkload >= 70) {
                                                $barColor = 'bg-blue-600'; // blue for moderate/high
                                            }
                                        @endphp
                                        <div>
                                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                                <span class="text-slate-700 font-bold">{{ $name }}</span>
                                                <span class="font-mono text-slate-800 font-bold">{{ $totalWorkload }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="h-full rounded-full {{ $barColor }} transition-all" style="width: {{ min($totalWorkload, 100) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="border-t border-slate-50 mt-5 pt-3 text-center">
                                    <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                        {{ __('Lihat Laporan Detail') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Optimasi SDM Promo Card -->
                        <div class="relative rounded-2xl overflow-hidden h-[130px] shadow-sm group">
                            <img src="/images/sdm_optimization.png" alt="Optimasi SDM" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/50 to-transparent flex flex-col justify-end p-4">
                                <h4 class="text-xs font-bold text-white tracking-wider uppercase mb-0.5">{{ __('Optimasi SDM') }}</h4>
                                <p class="text-[10px] font-bold text-slate-200 leading-snug">
                                    {{ __('Rasio efisiensi meningkat 15% sejak kuartal terakhir.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Notes Form Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                {{ __('Catatan Perencanaan SDM') }}
                            </h4>
                            <form action="{{ route('projects.human-resource.update', $project->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <textarea name="notes" rows="4" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 mb-3 placeholder-slate-400" placeholder="Masukkan catatan perencanaan SDM... ">{{ old('notes', $hrPlan->notes) }}</textarea>
                                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    {{ __('Simpan Catatan') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Rincian Item (Editable Table) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-slate-50 pb-4 mb-5 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-base text-slate-800">{{ __('Daftar Rincian Kebutuhan SDM') }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 font-medium">{{ __('Kebutuhan peran, kompetensi, jobdesk, PIC, dan beban kerja.') }}</p>
                                </div>
                                <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambah Peran') }}
                                </button>
                            </div>

                            <!-- Table -->
                            @if($hrItems->isEmpty())
                                <div class="p-16 text-center">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-users text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Alokasi SDM Kosong') }}</h5>
                                    <p class="text-xs text-slate-500 mb-4">{{ __('Belum ada rincian alokasi kebutuhan tim pelaksana untuk proyek ini.') }}</p>
                                    <button type="button" onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition gap-1.5 shadow-sm">
                                        <i class="fas fa-plus"></i>
                                        {{ __('Tambahkan Tim Pertama') }}
                                    </button>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-slate-50">
                                            <thead>
                                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    <th class="py-3">{{ __('ITEM WBS') }}</th>
                                                    <th class="py-3 px-3">{{ __('PERAN') }}</th>
                                                    <th class="py-3 px-3">{{ __('KEAHLIAN') }}</th>
                                                    <th class="py-3 px-3">{{ __('PIC') }}</th>
                                                    <th class="py-3 text-center">{{ __('BEBAN (%)') }}</th>
                                                    <th class="py-3 text-right">{{ __('DURASI / QTY') }}</th>
                                                    <th class="py-3 text-right pr-2">{{ __('AKSI') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50 text-xs">
                                                @foreach($hrItems as $item)
                                                    @php
                                                        $skills = array_map('trim', explode(',', $item->required_skill));
                                                        $badgeStyles = [
                                                            'bg-[#E0F2FE] text-[#0284C7] border-[#BAE6FD]',
                                                            'bg-[#F3E8FF] text-[#7E22CE] border-[#E9D5FF]',
                                                            'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]',
                                                            'bg-[#FFE4E6] text-[#E11D48] border-[#FECDD3]',
                                                            'bg-[#FEF3C7] text-[#D97706] border-[#FDE68A]',
                                                        ];
                                                        
                                                        // Load styling
                                                        $loadPercent = $item->workload_percentage !== null ? $item->workload_percentage : 0;
                                                        $loadBadge = 'bg-slate-100 text-slate-700 border border-slate-200';
                                                        if ($loadPercent > 90) {
                                                            $loadBadge = 'bg-[#FFE4E6] text-[#E11D48] border-[#FECDD3]';
                                                        } elseif ($loadPercent >= 70) {
                                                            $loadBadge = 'bg-[#E0F2FE] text-[#0284C7] border-[#BAE6FD]';
                                                        } elseif ($loadPercent > 0) {
                                                            $loadBadge = 'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]';
                                                        }
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/30 transition duration-150">
                                                        <td class="py-4 pr-3 max-w-[140px]">
                                                            @if($item->wbsItem)
                                                                <div class="font-bold text-slate-800 text-sm truncate" title="{{ $item->wbsItem->title }}">
                                                                    {{ $item->wbsItem->title }}
                                                                </div>
                                                                <div class="text-[9px] text-slate-400 font-mono mt-0.5">WBS ID: #{{ $item->wbs_item_id }}</div>
                                                            @else
                                                                <span class="text-slate-400 italic text-[10px]">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div class="font-bold text-slate-800 text-sm">{{ $item->role_name }}</div>
                                                            <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" title="{{ $item->job_description }}">{{ $item->job_description }}</div>
                                                        </td>
                                                        <td class="py-4 px-3 max-w-[160px]">
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($skills as $idx => $skill)
                                                                    @if(!empty($skill))
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $badgeStyles[$idx % count($badgeStyles)] }}">
                                                                            {{ $skill }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            @if($item->person_in_charge)
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-[9px] shadow-sm shrink-0">
                                                                        {{ getInitials($item->person_in_charge) }}
                                                                    </div>
                                                                    <span class="font-bold text-slate-700 truncate max-w-[100px]">{{ $item->person_in_charge }}</span>
                                                                </div>
                                                            @else
                                                                <span class="text-slate-400 italic text-[10px]">{{ __('Belum ditentukan') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 text-center">
                                                            @if($item->workload_percentage !== null)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $loadBadge }}">
                                                                    {{ $item->workload_percentage }}%
                                                                </span>
                                                            @else
                                                                <span class="text-slate-400 font-mono">0%</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 text-right">
                                                            <span class="font-bold text-slate-800 block text-sm">{{ $item->quantity }} {{ __('Orang') }}</span>
                                                            @if($item->estimated_work_days)
                                                                <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">{{ $item->estimated_work_days }} {{ __('Hari') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 text-right pr-2">
                                                            <div class="inline-flex items-center gap-1.5">
                                                                <!-- Edit Button -->
                                                                <button type="button" 
                                                                        onclick='openEditModal({!! json_encode($item) !!})' 
                                                                        class="w-7 h-7 flex items-center justify-center text-amber-600 bg-amber-50 border border-amber-100 hover:bg-amber-600 hover:text-white rounded-lg shadow-sm transition"
                                                                        title="{{ __('Edit Item') }}">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <!-- Delete Button -->
                                                                <form action="{{ route('projects.human-resource.items.delete', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item perencanaan SDM ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="w-7 h-7 flex items-center justify-center text-rose-600 bg-rose-50 border border-rose-100 hover:bg-rose-600 hover:text-white rounded-lg shadow-sm transition"
                                                                            title="{{ __('Hapus Item') }}">
                                                                        <i class="fas fa-trash-alt text-xs"></i>
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
            @endif
        </div>
    </div>

    <!-- MODAL: ADD HR ITEM -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true" onclick="closeAddModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.human-resource.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-plus text-blue-600"></i>
                                {{ __('Tambah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="add_role_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="add_role_name" required value="{{ old('role_name') }}" placeholder="Contoh: Senior UI Designer, Lead Engineer" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="add_required_skill" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="add_required_skill" required rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Contoh: Figma, CSS, React, REST API... ">{{ old('required_skill') }}</textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="add_job_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="add_job_description" required rows="3" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Jelaskan peran tugas pekerjaan utama peran ini dalam proyek... ">{{ old('job_description') }}</textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="add_wbs_item_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="add_wbs_item_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}" {{ old('wbs_item_id') == $wbs->id ? 'selected' : '' }}>{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Manual Text) -->
                            <div>
                                <label for="add_person_in_charge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Person In Charge) (Optional)') }}</label>
                                <input type="text" name="person_in_charge" id="add_person_in_charge" value="{{ old('person_in_charge') }}" placeholder="Contoh: Christsando, Abid, dsb." class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Workload, Work Days, Quantity (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="add_workload_percentage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="add_workload_percentage" min="0" max="100" value="{{ old('workload_percentage') }}" placeholder="100" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                                <div>
                                    <label for="add_estimated_work_days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="add_estimated_work_days" min="1" value="{{ old('estimated_work_days') }}" placeholder="10" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                                <div>
                                    <label for="add_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Jumlah orang">{{ __('Qty') }}</label>
                                    <input type="number" name="quantity" id="add_quantity" required min="1" value="{{ old('quantity', 1) }}" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Keterangan tambahan... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true" onclick="closeEditModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="edit_role_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="edit_role_name" required placeholder="Contoh: Senior UI Designer" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="edit_required_skill" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="edit_required_skill" required rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Keahlian... "></textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="edit_job_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="edit_job_description" required rows="3" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Jobdesk... "></textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="edit_wbs_item_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="edit_wbs_item_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Manual Text) -->
                            <div>
                                <label for="edit_person_in_charge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Person In Charge) (Optional)') }}</label>
                                <input type="text" name="person_in_charge" id="edit_person_in_charge" placeholder="Contoh: Christsando, Abid, dsb." class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Workload, Work Days, Quantity (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="edit_workload_percentage" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="edit_workload_percentage" min="0" max="100" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                                <div>
                                    <label for="edit_estimated_work_days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="edit_estimated_work_days" min="1" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                                <div>
                                    <label for="edit_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" title="Jumlah orang">{{ __('Qty') }}</label>
                                    <input type="number" name="quantity" id="edit_quantity" required min="1" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50" placeholder="Catatan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
