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
                            {{ __('Alokasi & Kapasitas Tim') }}
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
                        @if($isPmo && $isDraft)
                            <a href="{{ route('projects.human-resource.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1329] hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                <i class="fas fa-edit"></i>
                                {{ __('Kelola Perencanaan') }}
                            </a>
                        @endif
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

            @if(session('info'))
                <div class="p-4 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="font-medium">{{ session('info') }}</span>
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
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 shadow-sm flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold">{{ __('Draf Perencanaan SDM (Belum Final)') }}</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ __('Data alokasi personil SDM masih dalam status draf dan belum difinalisasi oleh PMO.') }}
                                </p>
                            </div>
                        </div>
                        @if($isPmo && $hrItems->count() > 0)
                            <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                    <i class="fas fa-check-circle"></i>
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
                        <div class="absolute right-3 bottom-1 text-[#1E293B]/60 text-6xl pointer-events-none font-bold">
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
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Kebutuhan peran, kompetensi, PIC, dan alokasi beban kerja.') }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-xs transition gap-1.5 shadow-sm">
                                <i class="fas fa-filter text-slate-400"></i>
                                {{ __('Filter') }}
                            </button>
                            <button type="button" onclick="alert('Mengekspor alokasi SDM proyek...');" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-xs transition gap-1.5 shadow-sm">
                                <i class="fas fa-download text-slate-400"></i>
                                {{ __('Ekspor') }}
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
                            <p class="text-xs text-slate-500">{{ __('Belum ada rincian alokasi kebutuhan tim pelaksana untuk proyek ini.') }}</p>
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

                <!-- Notes Card (Displaying Notes elegantly at the bottom) -->
                @if($hrPlan->notes)
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <h4 class="font-extrabold text-xs uppercase text-slate-400 tracking-wider flex items-center gap-2 mb-3">
                            <i class="fas fa-sticky-note text-[#0B1329]"></i>
                            {{ __('Catatan Perencanaan SDM') }}
                        </h4>
                        <p class="text-xs text-slate-500 leading-relaxed font-semibold bg-slate-50 p-4 rounded-xl border border-slate-100/60 whitespace-pre-line">
                            {{ $hrPlan->notes }}
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
