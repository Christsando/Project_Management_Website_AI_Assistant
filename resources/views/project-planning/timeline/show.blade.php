<x-app-layout>
    <x-slot name="header">
        <x-header-component :title="'Project Planning: Timeline'" icon="fa-solid fa-calendar-alt text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        @php
            $userRole = strtolower(Auth::user()->role);
            $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
            $isDraft = !$isTimelineFinalized;
        @endphp

        <!-- Top Navigation Tab Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-200 pb-3 mb-6 gap-4">
            <div class="flex items-center gap-6">
                <span class="text-lg font-extrabold text-blue-600 tracking-tight">KelolaIN</span>
                <span class="text-slate-300">|</span>
                <a href="{{ route('projects.show', $project->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                    {{ __('Ringkasan') }}
                </a>
                <a href="{{ route('projects.wbs.show', $project->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                    {{ __('Work Breakdown Structure') }}
                </a>
                <a href="{{ route('projects.timeline.show', $project->id) }}" class="text-xs font-bold text-blue-600 border-b-2 border-blue-600 pb-3.5 -mb-4 transition">
                    {{ __('Timeline') }}
                </a>
            </div>
            <div>
                <a href="{{ route('project-planning.timeline.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-800 font-bold rounded-xl text-xs transition shadow-sm gap-2">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    {{ __('Kembali ke Daftar Timeline') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-500 text-sm"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Blue Status Banner -->
        <div class="mb-6 p-6 rounded-3xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <!-- Decorative Vector Design -->
            <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 pointer-events-none hidden md:block">
                <svg class="w-full h-full text-white" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" stroke="currentColor" stroke-width="0.5">
                    <path d="M 0 10 L 100 90 M 0 90 L 100 10 M 10 0 L 10 100" />
                    <circle cx="50" cy="50" r="30" />
                </svg>
            </div>

            <div class="space-y-4 max-w-2xl relative z-10">
                <h3 class="text-xl font-extrabold tracking-tight">
                    {{ __('Timeline Planning & Gantt Chart') }}
                </h3>
                <p class="text-xs text-blue-50/90 leading-relaxed font-medium">
                    {{ __('Proyek:') }} <span class="font-extrabold text-white">{{ $project->title }}</span>
                </p>
                <div class="inline-flex items-center gap-2 py-1.5 px-3 bg-white/10 border border-white/10 rounded-full text-[10px] font-bold tracking-wide">
                    @if($isTimelineFinalized)
                        <i class="fas fa-check-circle text-emerald-300"></i>
                        <span>{{ __('Timeline Finalized - Siap untuk Tahap Planning Selanjutnya') }}</span>
                    @else
                        <i class="fas fa-info-circle text-blue-200"></i>
                        <span>{{ __('Siap digunakan untuk Budget Planning dan Human Resource Planning') }}</span>
                    @endif
                </div>
            </div>

            <div class="shrink-0 relative z-10">
                @if($isTimelineFinalized)
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-blue-100 uppercase tracking-wider block">{{ __('Status Timeline') }}</span>
                            <span class="text-xs font-extrabold text-white block mt-0.5">{{ __('TELAH DIFINALISASI') }}</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        @if($isPmo && $wbsItemsCount > $timelineItemsCount)
                            <a href="{{ route('projects.timeline.create', $project->id) }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-slate-50 text-blue-700 font-extrabold rounded-2xl text-xs shadow-md transition duration-200 transform hover:-translate-y-0.5">
                                <i class="fas fa-plus text-blue-600"></i>
                                {{ __('Jadwalkan Tugas') }}
                            </a>
                        @endif

                        @if($isPmo && $timelineItemsCount > 0 && $wbsItemsCount === $timelineItemsCount)
                            <form action="{{ route('projects.timeline.finalize', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi timeline ini? Setelah finalized, seluruh jadwal timeline akan dikunci dan tidak dapat diubah atau dihapus.');">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold rounded-2xl text-xs shadow-md transition duration-200 transform hover:-translate-y-0.5 border border-emerald-400">
                                    <i class="fas fa-check-double"></i>
                                    {{ __('Finalisasi Timeline') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Task Completion Warnings -->
        @if(!$isTimelineFinalized && $wbsItemsCount > $timelineItemsCount)
            <div class="mb-6 p-4.5 bg-amber-50/50 border border-amber-200/60 text-amber-800 rounded-2xl text-xs flex items-center gap-3 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h5 class="font-bold">{{ __('Jadwal Belum Lengkap') }}</h5>
                    <p class="text-[11px] text-amber-700/90 font-medium mt-0.5">
                        {{ __('Ada ' . ($wbsItemsCount - $timelineItemsCount) . ' tugas WBS yang belum dijadwalkan. Jadwalkan seluruh tugas WBS untuk mengaktifkan tombol Finalisasi.') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Visual View Selector Tabs -->
        <div class="flex items-center border-b border-slate-200 mb-6 relative">
            <button type="button" id="tab-table-btn" class="px-6 py-3 font-extrabold text-xs border-b-2 border-blue-600 text-blue-600 focus:outline-none transition flex items-center gap-2 tracking-wider uppercase">
                <i class="fas fa-table text-sm"></i>{{ __('Tabel Rincian Jadwal') }}
            </button>
            <button type="button" id="tab-gantt-btn" class="px-6 py-3 font-extrabold text-xs border-b-2 border-transparent text-slate-400 hover:text-slate-600 focus:outline-none transition flex items-center gap-2 tracking-wider uppercase">
                <i class="fas fa-chart-gantt text-sm"></i>{{ __('Visualisasi Gantt Chart') }}
            </button>
        </div>

        <!-- Tab 1: Table View -->
        <div id="tab-table-content" class="block">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                @if($timelineItems->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <h4 class="font-extrabold text-lg text-slate-800 mb-1">{{ __('Belum ada tugas dijadwalkan') }}</h4>
                        <p class="text-xs text-slate-500 mb-4">{{ __('Jadwal pelaksanaan kerja (timeline) belum dibuat.') }}</p>
                        @if($isPmo && $isDraft)
                            <a href="{{ route('projects.timeline.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition gap-2">
                                <i class="fas fa-plus text-[10px]"></i>
                                {{ __('Jadwalkan Tugas Pertama') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">{{ __('Item WBS') }}</th>
                                    <th class="px-6 py-4">{{ __('Jadwal Pelaksanaan') }}</th>
                                    <th class="px-6 py-4">{{ __('Durasi') }}</th>
                                    <th class="px-6 py-4">{{ __('Milestone') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                                @foreach($wbsItems as $wbs)
                                    @include('project-planning.timeline._timeline_row', ['wbs' => $wbs, 'depth' => 0, 'project' => $project, 'isTimelineFinalized' => $isTimelineFinalized])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 2: Gantt Chart View -->
        <div id="tab-gantt-content" class="hidden">
            @if($timelineItems->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 border border-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-chart-gantt text-2xl"></i>
                    </div>
                    <h4 class="font-extrabold text-lg text-slate-800 mb-1">{{ __('Gantt Chart Kosong') }}</h4>
                    <p class="text-xs text-slate-500">{{ __('Jadwalkan minimal satu tugas untuk memvisualisasikan Gantt Chart.') }}</p>
                </div>
            @else
                @php
                    $dates = [];
                    $months = [];
                    if ($minDate && $maxDate) {
                        $tempDate = $minDate->copy();
                        while ($tempDate->lte($maxDate)) {
                            $dates[] = $tempDate->copy();
                            $monthKey = $tempDate->format('F Y');
                            if (!isset($months[$monthKey])) {
                                $months[$monthKey] = 0;
                            }
                            $months[$monthKey]++;
                            $tempDate->addDay();
                        }
                    }
                    $totalColumns = count($dates);
                @endphp



                <!-- Gantt Scroll Container -->
                <div class="border border-slate-200/80 rounded-2xl overflow-hidden bg-white shadow-sm flex flex-col mb-4">
                    <div class="overflow-x-auto relative">
                        <div style="min-width: {{ 320 + ($totalColumns * 48) }}px" class="flex flex-col">
                            <!-- Gantt Header -->
                            <div class="flex bg-slate-50/70 border-b border-slate-100 items-stretch sticky top-0 z-30">
                                <!-- Left Header Column -->
                                <div class="w-80 shrink-0 border-r border-slate-100 flex items-center px-5 font-extrabold text-[9px] text-slate-400 uppercase tracking-wider sticky left-0 bg-slate-50 z-40">
                                    {{ __('Tugas & Struktur WBS') }}
                                </div>
                                <!-- Right Header Calendar Columns -->
                                <div class="flex-1 flex flex-col min-w-0">
                                    <!-- Month/Year Header Row -->
                                    <div class="flex border-b border-slate-200/50 bg-slate-50/50 items-center">
                                        @foreach($months as $monthName => $daysCount)
                                            <div class="text-[9px] font-extrabold text-slate-400 border-r border-slate-100/50 text-center uppercase tracking-wider py-1.5 shrink-0" 
                                                 style="width: {{ $daysCount * 48 }}px">
                                                {{ $monthName }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <!-- Day Number Header Row -->
                                    <div class="flex bg-white items-center">
                                        @foreach($dates as $date)
                                            <div class="text-[10px] font-extrabold text-slate-500 border-r border-slate-100/30 text-center w-12 shrink-0 py-2.5">
                                                {{ $date->format('d') }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Gantt Rows -->
                            <div class="divide-y divide-slate-100">
                                @foreach($wbsItems as $wbs)
                                    @include('project-planning.timeline._gantt_row', [
                                        'wbs' => $wbs, 
                                        'depth' => 0, 
                                        'projectDurationDays' => $totalColumns, 
                                        'minDate' => $minDate
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Legend / Helpers -->
                <div class="flex flex-wrap items-center justify-start gap-6 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wide py-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>{{ __('Klik & Seret untuk mengubah durasi') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-left-right text-xs text-slate-400"></i>
                        <span>{{ __('Scroll horizontal untuk linimasa penuh') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 bg-amber-500 rotate-45 border border-amber-300 shadow-sm shrink-0"></div>
                        <span>{{ __('Penanda Milestone') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Tab switching Vanilla Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabTableBtn = document.getElementById('tab-table-btn');
            const tabGanttBtn = document.getElementById('tab-gantt-btn');
            const tabTableContent = document.getElementById('tab-table-content');
            const tabGanttContent = document.getElementById('tab-gantt-content');

            if (tabTableBtn && tabGanttBtn && tabTableContent && tabGanttContent) {
                tabTableBtn.addEventListener('click', function () {
                    // Update buttons styling
                    tabTableBtn.classList.add('border-blue-600', 'text-blue-600');
                    tabTableBtn.classList.remove('border-transparent', 'text-slate-400');
                    tabGanttBtn.classList.add('border-transparent', 'text-slate-400');
                    tabGanttBtn.classList.remove('border-blue-600', 'text-blue-600');

                    // Toggle contents
                    tabTableContent.classList.remove('hidden');
                    tabTableContent.classList.add('block');
                    tabGanttContent.classList.remove('block');
                    tabGanttContent.classList.add('hidden');
                });

                tabGanttBtn.addEventListener('click', function () {
                    // Update buttons styling
                    tabGanttBtn.classList.add('border-blue-600', 'text-blue-600');
                    tabGanttBtn.classList.remove('border-transparent', 'text-slate-400');
                    tabTableBtn.classList.add('border-transparent', 'text-slate-400');
                    tabTableBtn.classList.remove('border-blue-600', 'text-blue-600');

                    // Toggle contents
                    tabGanttContent.classList.remove('hidden');
                    tabGanttContent.classList.add('block');
                    tabTableContent.classList.remove('block');
                    tabTableContent.classList.add('hidden');
                });
            }

            // Collapse/Expand Section function for Gantt Chart Tree
            window.wbsToggleSection = function(sectionId, element) {
                const target = document.getElementById(sectionId);
                if (target) {
                    const isHidden = target.style.display === 'none';
                    target.style.display = isHidden ? '' : 'none';
                    
                    // Toggle chevron icon
                    const icon = element.querySelector('i');
                    if (icon) {
                        if (isHidden) {
                            icon.className = 'fas fa-chevron-down text-[10px]';
                        } else {
                            icon.className = 'fas fa-chevron-right text-[10px]';
                        }
                    }
                }
            };
        });
    </script>
</x-app-layout>
