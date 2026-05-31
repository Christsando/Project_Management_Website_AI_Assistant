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

        // Calculations for workload average and capacity status
        $avgWorkload = 0;
        $overloadCount = 0;
        $optimalCount = 0;
        $underloadCount = 0;
        if ($hrPlan && $hrItems->count() > 0) {
            $avgWorkload = round($hrItems->avg('workload_percentage') ?: 0);
            
            $pics = $hrItems->whereNotNull('person_in_charge')->where('person_in_charge', '!=', '')->groupBy('person_in_charge');
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

    <div class="pl-4 pt-2 pb-12">
        <div class="max-w-6xl mx-auto space-y-6">
            
            <!-- Top Sub-Navigation Tabs (Redesigned as sleek pill tabs) -->
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl max-w-md">
                <a href="{{ route('projects.human-resource.show', $project->id) }}" class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg bg-white text-slate-800 shadow-sm transition">
                    {{ __('Human Resource') }}
                </a>
                <a href="{{ route('projects.timeline.show', $project->id) }}" class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition">
                    {{ __('Gantt Chart') }}
                </a>
                <a href="{{ route('projects.budget.show', $project->id) }}" class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition">
                    {{ __('Budgeting') }}
                </a>
            </div>

            <!-- Back Navigation & Header Section -->
            <div class="space-y-4">
                <a href="{{ route('project-planning.human-resource.index') }}" class="inline-flex items-center text-[10px] font-bold text-slate-400 hover:text-slate-600 transition gap-1.5 uppercase tracking-wider">
                    <i class="fas fa-arrow-left text-[8px]"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Perencanaan Proyek') }} > {{ __('Perencanaan SDM') }}</div>
                        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight mt-1">
                            {{ __('Kelola Perencanaan SDM (HR Plan)') }}
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ __('Kelola beban kerja personil dan alokasikan peran strategis untuk memastikan keberhasilan proyek tepat waktu.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                            <i class="fas fa-project-diagram text-slate-400"></i>
                            {{ __('Hub Proyek') }}
                        </a>
                        <a href="{{ route('projects.human-resource.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                            <i class="fas fa-redo text-slate-400"></i>
                            {{ __('Reset Halaman') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-100 text-rose-850 rounded-xl text-xs shadow-sm">
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
            @if($hrPlan)
                @if($hrPlan->status === 'finalized')
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 shadow-sm flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold">{{ __('Siap digunakan untuk Risk Management') }}</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ __('Data alokasi personil SDM Anda telah divalidasi secara permanen dan siap diintegrasikan dengan modul manajemen risiko.') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="p-5 bg-emerald-50 border border-emerald-100 text-emerald-850 rounded-2xl text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start sm:items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h5 class="font-bold text-sm text-slate-850">{{ __('RAB/SDM dapat difinalisasi') }}</h5>
                                <p class="text-xs text-emerald-650 font-semibold mt-0.5">{{ __('Rencana alokasi SDM dapat difinalisasi karena draf struktur peran tim telah terisi.') }}</p>
                            </div>
                        </div>
                        @if($isPmo && $hrItems->count() > 0)
                            <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi perencanaan SDM ini? Setelah finalized, seluruh alokasi SDM dan tugas akan dikunci dan tidak dapat diubah lagi.');">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                    <i class="fas fa-check-double text-[10px]"></i>
                                    {{ __('Finalisasi Perencanaan SDM') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Plan content -->
            @if(!$hrPlan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h4 class="font-extrabold text-slate-800 mb-1 text-base">{{ __('Belum Ada Perencanaan SDM') }}</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">{{ __('Perencanaan sumber daya manusia (SDM) proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Metric Cards Row (Span full width) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card 1: Total Personil -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-lg shadow-sm border border-blue-100/50 shrink-0">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                {{ __('TOTAL PERSONIL') }}
                            </span>
                            <h3 class="text-3xl font-black text-slate-800 mt-1 tracking-tight">
                                {{ $totalResources }}
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400 mt-1 block">
                                {{ __('+2 dibanding bulan lalu') }}
                            </span>
                        </div>
                    </div>

                    <!-- Card 2: Beban Rata-Rata -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-lg shadow-sm border border-blue-100/50 shrink-0">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                {{ __('BEBAN RATA-RATA') }}
                            </span>
                            <h3 class="text-3xl font-black text-slate-800 mt-1 tracking-tight">
                                {{ $avgWorkload }}%
                            </h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden mt-2.5">
                                <div class="h-full rounded-full bg-blue-600 transition-all duration-300" style="width: {{ $avgWorkload }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Status Kapasitas Proyek -->
                    <div class="bg-[#0B1329] text-white rounded-2xl p-5 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        <!-- Background graph icon watermark -->
                        <div class="absolute right-3 bottom-1 text-[#1E293B]/65 text-6xl pointer-events-none font-bold">
                            <i class="fas fa-chart-bar opacity-30"></i>
                        </div>
                        <div class="relative z-10">
                            <h4 class="text-xs font-bold text-white tracking-wide uppercase">{{ __('Status Kapasitas Proyek') }}</h4>
                            <p class="text-[10px] font-medium text-slate-300 mt-1 leading-snug">
                                {{ __('Tim saat ini berada dalam ambang batas optimal (60% - 85%).') }}
                            </p>
                        </div>
                        <div class="flex gap-1.5 mt-4 relative z-10">
                            <span class="px-2 py-1 rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-extrabold">
                                {{ $overloadCount }} Overload
                            </span>
                            <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-extrabold">
                                {{ $optimalCount }} Optimal
                            </span>
                            <span class="px-2 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[9px] font-extrabold">
                                {{ $underloadCount }} Underload
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Main Resource List Table Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <!-- Title and Actions -->
                    <div class="border-b border-slate-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ __('Daftar Alokasi Sumber Daya') }}</h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Kebutuhan peran, kompetensi, PIC, alokasi beban kerja, dan aksi.') }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                <i class="fas fa-plus text-[9px]"></i>
                                {{ __('Tambah Peran') }}
                            </button>
                            <button type="button" class="p-1.5 border border-slate-200 rounded-lg text-slate-400 hover:text-slate-650 hover:bg-slate-50 text-xs transition" title="Filter">
                                <i class="fas fa-filter"></i>
                            </button>
                            <button type="button" class="p-1.5 border border-slate-200 rounded-lg text-slate-400 hover:text-slate-655 hover:bg-slate-50 text-xs transition" title="Export">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    @if($hrItems->isEmpty())
                        <div class="p-16 text-center">
                            <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Alokasi SDM Kosong') }}</h5>
                            <p class="text-xs text-slate-500 mb-4">{{ __('Belum ada rincian alokasi kebutuhan tim pelaksana untuk proyek ini.') }}</p>
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-800 border border-slate-200 rounded-xl text-xs font-bold hover:bg-slate-250 transition gap-1.5 shadow-sm">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambahkan Tim Pertama') }}
                            </button>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="py-4 px-6">{{ __('ITEM WBS') }}</th>
                                        <th class="py-4 px-4">{{ __('PERAN') }}</th>
                                        <th class="py-4 px-4">{{ __('KEAHLIAN') }}</th>
                                        <th class="py-4 px-4">{{ __('PERSONIL (PIC)') }}</th>
                                        <th class="py-4 px-4 text-center">{{ __('WORKLOAD %') }}</th>
                                        <th class="py-4 px-6 text-right">{{ __('DURASI') }}</th>
                                        <th class="py-4 px-6 text-right pr-6">{{ __('AKSI') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    @foreach($hrItems as $item)
                                        @php
                                            $skills = array_map('trim', explode(',', $item->required_skill));
                                            
                                            // Determine workload label and bar styles
                                            $loadPercent = $item->workload_percentage !== null ? $item->workload_percentage : 0;
                                            $loadBarColor = 'bg-slate-450'; 
                                            $loadLabel = 'UNDERLOAD';
                                            $loadLabelClass = 'text-slate-500';
                                            
                                            if ($loadPercent > 85) {
                                                $loadBarColor = 'bg-rose-500';
                                                $loadLabel = 'OVERLOAD';
                                                $loadLabelClass = 'text-rose-500';
                                            } elseif ($loadPercent >= 60) {
                                                $loadBarColor = 'bg-slate-700';
                                                $loadLabel = 'OPTIMAL';
                                                $loadLabelClass = 'text-slate-750';
                                            }
                                        @endphp
                                        <tr class="hover:bg-slate-50/30 transition duration-150">
                                            <td class="py-4 px-6 max-w-[160px]">
                                                @if($item->wbsItem)
                                                    <div class="font-extrabold text-slate-800 text-sm truncate" title="{{ $item->wbsItem->title }}">
                                                        {{ $item->wbsItem->title }}
                                                    </div>
                                                    <div class="text-[9px] text-slate-400 font-bold mt-0.5 uppercase tracking-wider">
                                                        @if($item->wbsItem->parent)
                                                            Fase {{ $item->wbsItem->parent->title }}
                                                        @else
                                                            Fase Perencanaan
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic text-[10px]">-</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-slate-700 font-bold">
                                                {{ $item->role_name }}
                                            </td>
                                            <td class="py-4 px-4 max-w-[180px]">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($skills as $idx => $skill)
                                                        @if(!empty($skill))
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-750 border border-purple-100">
                                                                {{ $skill }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($item->teamMember)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-full bg-[#0B1329] text-white flex items-center justify-center font-black text-[9px] shadow-sm shrink-0">
                                                            {{ getInitials($item->teamMember->name) }}
                                                        </div>
                                                        <span class="font-bold text-slate-700 truncate max-w-[120px]">{{ $item->teamMember->name }}</span>
                                                    </div>
                                                @elseif($item->person_in_charge)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-[9px] shadow-sm shrink-0">
                                                            {{ getInitials($item->person_in_charge) }}
                                                        </div>
                                                        <span class="font-bold text-slate-600 truncate max-w-[120px]">{{ $item->person_in_charge }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic text-[10px]">{{ __('Belum ditentukan') }}</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="w-24 mx-auto">
                                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-700 mb-1">
                                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden flex-1 mr-2 border border-slate-200/50">
                                                            <div class="h-full rounded-full {{ $loadBarColor }} transition-all duration-300" style="width: {{ $loadPercent }}%"></div>
                                                        </div>
                                                        <span class="font-mono">{{ $loadPercent }}%</span>
                                                    </div>
                                                    <span class="text-[8px] font-black uppercase tracking-wider block text-left {{ $loadLabelClass }}">{{ $loadLabel }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-right font-extrabold text-slate-800 font-mono text-sm">
                                                @if($item->estimated_work_days)
                                                    {{ $item->estimated_work_days }} Hari
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-right pr-6">
                                                <!-- Dropdown Ellipsis Menu using Alpine.js -->
                                                <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false">
                                                    <button type="button" @click="open = !open" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition">
                                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                                    </button>
                                                    <div x-show="open" class="origin-top-right absolute right-0 mt-1 w-32 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 focus:outline-none divide-y divide-slate-55" style="display: none;">
                                                        <div class="py-1">
                                                            <button type="button" onclick='openEditModal({!! json_encode($item) !!})' class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs text-amber-700 hover:bg-slate-50 transition font-bold">
                                                                <i class="fas fa-edit text-[10px]"></i> {{ __('Ubah') }}
                                                            </button>
                                                        </div>
                                                        <div class="py-1">
                                                            <form action="{{ route('projects.human-resource.items.delete', [$project->id, $item->id]) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item perencanaan SDM ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-slate-50 transition font-bold">
                                                                    <i class="fas fa-trash-alt text-[10px]"></i> {{ __('Hapus') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Pagination / Stats -->
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500 font-medium">
                            <div id="pagination-stats">
                                {{ __('Menampilkan ') }}<span class="font-bold text-slate-700" id="visible-count">{{ $hrItems->count() }}</span>{{ __(' dari ') }}<span class="font-bold text-slate-700">{{ $hrItems->count() }}</span>{{ __(' personil') }}
                            </div>
                            <div class="inline-flex gap-1">
                                <button type="button" disabled class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Sebelumnya</button>
                                <button type="button" class="px-3 py-1 border border-slate-800 bg-slate-800 text-white rounded-lg text-[11px] font-bold">1</button>
                                <button type="button" disabled class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Selanjutnya</button>
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
                            <h4 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider">{{ __('Distribusi Keahlian') }}</h4>
                        </div>
                        <div class="space-y-3.5 text-xs font-bold text-slate-750">
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Frontend Development</span>
                                    <span class="font-mono text-slate-800">40%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-700 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Backend Development</span>
                                    <span class="font-mono text-slate-800">35%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-600 rounded-full" style="width: 35%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>UI/UX Design</span>
                                    <span class="font-mono text-slate-800">15%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-500 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>DevOps & Testing</span>
                                    <span class="font-mono text-slate-800">10%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-400 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Prediksi Ketersediaan Tim (CSS Bar Chart) -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <h4 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                {{ __('Prediksi Ketersediaan Tim') }}
                            </h4>
                            <i class="fas fa-chart-bar text-slate-450"></i>
                        </div>
                        <!-- Pure CSS vertical bars chart matching mockup -->
                        <div class="flex items-end justify-between h-44 pt-4 px-2 font-mono text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-100 rounded-lg h-20 transition-all duration-300 hover:bg-slate-200"></div>
                                <span>JAN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-150 rounded-lg h-24 transition-all duration-300 hover:bg-slate-200"></div>
                                <span>FEB</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-[#0B1329] rounded-lg h-32 shadow-sm transition-all duration-300 hover:bg-slate-800"></div>
                                <span class="text-slate-800 font-black">MAR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-200 rounded-lg h-28 transition-all duration-300 hover:bg-slate-350"></div>
                                <span>APR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-100 rounded-lg h-12 transition-all duration-300 hover:bg-slate-150"></div>
                                <span>MEI</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-150 rounded-lg h-16 transition-all duration-300 hover:bg-slate-200"></div>
                                <span>JUN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-9 bg-slate-200 rounded-lg h-20 transition-all duration-300 hover:bg-slate-250"></div>
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
                    <form action="{{ route('projects.human-resource.update', $project->id) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <textarea name="notes" rows="3" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-150 text-slate-700" placeholder="Masukkan catatan perencanaan SDM... ">{{ old('notes', $hrPlan->notes) }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="closeAddModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.human-resource.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-plus text-[#0B1329]"></i>
                                {{ __('Tambah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="add_role_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="add_role_name" required value="{{ old('role_name') }}" placeholder="Contoh: Senior UI Designer, Lead Engineer" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="add_required_skill" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="add_required_skill" required rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Contoh: Figma, CSS, React, REST API... ">{{ old('required_skill') }}</textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="add_job_description" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="add_job_description" required rows="3" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Jelaskan peran tugas pekerjaan utama peran ini dalam proyek... ">{{ old('job_description') }}</textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="add_wbs_item_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="add_wbs_item_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}" {{ old('wbs_item_id') == $wbs->id ? 'selected' : '' }}>{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Team Member Dropdown & Fallback) -->
                            <div>
                                <label for="add_team_member_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Pilih Anggota Tim (PIC)') }}</label>
                                <select name="team_member_id" id="add_team_member_id" onchange="updateTeamMemberInfo(this, 'add')" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Masukkan PIC Manual / Bebas') }} --</option>
                                    @foreach($teamMembers as $member)
                                        <option value="{{ $member->id }}" 
                                                data-role="{{ $member->role_name }}" 
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
                            <div id="add_member_info_box" class="hidden p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-xs space-y-1.5">
                                <div class="flex justify-between font-bold text-slate-700">
                                    <span>Peran: <span id="add_info_role" class="text-blue-700 font-extrabold"></span></span>
                                    <span>Status: <span id="add_info_status" class="px-1.5 py-0.5 rounded text-[9px] font-extrabold"></span></span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-semibold">
                                    Keahlian: <span id="add_info_skills"></span>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-600 font-extrabold border-t border-blue-100/50 pt-1.5">
                                    <span>Current Workload: <span id="add_info_workload"></span>%</span>
                                    <span>Remaining Capacity: <span id="add_info_remaining"></span>%</span>
                                </div>
                            </div>

                            <div id="add_pic_manual_container">
                                <label for="add_person_in_charge" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Manual)') }}</label>
                                <input type="text" name="person_in_charge" id="add_person_in_charge" value="{{ old('person_in_charge') }}" placeholder="Contoh: John Doe" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Workload & Work Days (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="add_workload_percentage" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="add_workload_percentage" min="0" max="100" value="{{ old('workload_percentage') }}" placeholder="100" class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                                <div>
                                    <label for="add_estimated_work_days" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="add_estimated_work_days" min="1" value="{{ old('estimated_work_days') }}" placeholder="10" class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Keterangan tambahan... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-150 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="closeEditModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Peran & Alokasi SDM') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                            <!-- Nama Peran -->
                            <div>
                                <label for="edit_role_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama Peran / Jabatan') }}</label>
                                <input type="text" name="role_name" id="edit_role_name" required placeholder="Contoh: Senior UI Designer" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Keahlian yang dibutuhkan -->
                            <div>
                                <label for="edit_required_skill" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Keahlian yang Dibutuhkan (Skills)') }}</label>
                                <textarea name="required_skill" id="edit_required_skill" required rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Keahlian... "></textarea>
                            </div>

                            <!-- Deskripsi Pekerjaan -->
                            <div>
                                <label for="edit_job_description" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Jobdesk') }}</label>
                                <textarea name="job_description" id="edit_job_description" required rows="3" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Jobdesk... "></textarea>
                            </div>

                            <!-- Relasi Tugas WBS (Optional) -->
                            <div>
                                <label for="edit_wbs_item_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Tautkan Tugas WBS (Optional)') }}</label>
                                <select name="wbs_item_id" id="edit_wbs_item_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Tidak ditautkan ke WBS') }} --</option>
                                    @foreach($wbsItems as $wbs)
                                        <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PIC (Team Member Dropdown & Fallback) -->
                            <div>
                                <label for="edit_team_member_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Pilih Anggota Tim (PIC)') }}</label>
                                <select name="team_member_id" id="edit_team_member_id" onchange="updateTeamMemberInfo(this, 'edit')" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 bg-slate-50/10 text-slate-700">
                                    <option value="">-- {{ __('Masukkan PIC Manual / Bebas') }} --</option>
                                    @foreach($teamMembers as $member)
                                        <option value="{{ $member->id }}" 
                                                data-role="{{ $member->role_name }}" 
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
                            <div id="edit_member_info_box" class="hidden p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-xs space-y-1.5">
                                <div class="flex justify-between font-bold text-slate-700">
                                    <span>Peran: <span id="edit_info_role" class="text-blue-700 font-extrabold"></span></span>
                                    <span>Status: <span id="edit_info_status" class="px-1.5 py-0.5 rounded text-[9px] font-extrabold"></span></span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-semibold">
                                    Keahlian: <span id="edit_info_skills"></span>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-650 font-bold border-t border-blue-100/50 pt-1.5">
                                    <span>Current Workload: <span id="edit_info_workload"></span>%</span>
                                    <span>Remaining Capacity: <span id="edit_info_remaining"></span>%</span>
                                </div>
                            </div>

                            <div id="edit_pic_manual_container">
                                <label for="edit_person_in_charge" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Nama PIC (Manual)') }}</label>
                                <input type="text" name="person_in_charge" id="edit_person_in_charge" placeholder="Contoh: John Doe" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                            </div>

                            <!-- Workload & Work Days (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_workload_percentage" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Beban Kerja (0-100)%">{{ __('Load (%)') }}</label>
                                    <input type="number" name="workload_percentage" id="edit_workload_percentage" min="0" max="100" class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                                <div>
                                    <label for="edit_estimated_work_days" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Estimasi Hari Kerja">{{ __('Hari Kerja') }}</label>
                                    <input type="number" name="estimated_work_days" id="edit_estimated_work_days" min="1" class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Catatan (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-slate-800 focus:ring focus:ring-slate-100 placeholder-slate-400 bg-slate-50/10 text-slate-700" placeholder="Catatan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-150 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
            document.getElementById('edit_workload_percentage').value = item.workload_percentage !== null ? item.workload_percentage : '';
            document.getElementById('edit_estimated_work_days').value = item.estimated_work_days !== null ? item.estimated_work_days : '';
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
