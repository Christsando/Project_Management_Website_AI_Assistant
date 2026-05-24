<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.timeline.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar Timeline') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Timeline Planning & Gantt Chart') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                    </h3>
                </div>

                @php
                    $userRole = strtolower(Auth::user()->role);
                    $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
                    $isDraft = !$isTimelineFinalized;
                @endphp

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-gray-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>

                    @if($isPmo && $isDraft)
                        @if($wbsItemsCount > $timelineItemsCount)
                            <a href="{{ route('projects.timeline.create', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-1.5">
                                <i class="fas fa-plus"></i>
                                {{ __('Jadwalkan Tugas') }}
                            </a>
                        @endif

                        @if($timelineItemsCount > 0 && $wbsItemsCount === $timelineItemsCount)
                            <form action="{{ route('projects.timeline.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi timeline ini? Setelah finalized, seluruh jadwal timeline akan dikunci dan tidak dapat diubah atau dihapus.');">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                    <i class="fas fa-check-double"></i>
                                    {{ __('Finalisasi Timeline') }}
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

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

            <!-- Timeline Status Banner -->
            @if($isTimelineFinalized)
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-check-double text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('Timeline Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            {{ __('Siap digunakan untuk Budget Planning dan Human Resource Planning.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature text-gray-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf Timeline') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            @if($wbsItemsCount > $timelineItemsCount)
                                <span class="text-amber-700 font-semibold flex items-center gap-1 mb-1">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('Ada ' . ($wbsItemsCount - $timelineItemsCount) . ' tugas WBS yang belum dijadwalkan.') }}
                                </span>
                            @endif
                            {{ __('Jadwalkan seluruh tugas WBS terlebih dahulu untuk dapat memfinalisasi Timeline Planning.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 mb-6">
                <button type="button" id="tab-table-btn" class="px-6 py-3 font-semibold text-sm border-b-2 border-primary text-primary focus:outline-none transition">
                    <i class="fas fa-table mr-2"></i>{{ __('Tabel Rincian Jadwal') }}
                </button>
                <button type="button" id="tab-gantt-btn" class="px-6 py-3 font-semibold text-sm border-b-2 border-transparent text-secondaryText hover:text-primaryText focus:outline-none transition">
                    <i class="fas fa-stream mr-2"></i>{{ __('Visualisasi Gantt Chart') }}
                </button>
            </div>

            <!-- Tab 1: Table View -->
            <div id="tab-table-content" class="block">
                <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                    @if($timelineItems->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Belum ada tugas dijadwalkan') }}</h4>
                            <p class="text-sm text-secondaryText mb-4">{{ __('Jadwal pelaksanaan kerja (timeline) belum dibuat.') }}</p>
                            @if($isPmo && $isDraft)
                                <a href="{{ route('projects.timeline.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md transition gap-2">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    {{ __('Jadwalkan Tugas Pertama') }}
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-[#e3e3e0] text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                        <th class="px-6 py-4">{{ __('Item WBS') }}</th>
                                        <th class="px-6 py-4">{{ __('Jadwal Pelaksanaan') }}</th>
                                        <th class="px-6 py-4">{{ __('Durasi') }}</th>
                                        <th class="px-6 py-4">{{ __('Milestone') }}</th>
                                        <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
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
                <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                    @if($timelineItems->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-stream text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Gantt Chart Kosong') }}</h4>
                            <p class="text-sm text-secondaryText">{{ __('Jadwalkan minimal satu tugas untuk memvisualisasikan Gantt Chart.') }}</p>
                        </div>
                    @else
                        @php
                            $projectDurationDays = $minDate && $maxDate ? $minDate->diffInDays($maxDate) + 1 : 0;
                        @endphp
                        
                        <!-- Gantt Calendar Time Header Scale -->
                        <div class="grid grid-cols-12 gap-4 border-b border-gray-200 pb-3 items-center font-bold text-xs text-secondaryText uppercase tracking-wider">
                            <div class="col-span-4">{{ __('Tugas WBS') }}</div>
                            <div class="col-span-8 flex justify-between items-center relative h-6">
                                <span>{{ $minDate->format('d M Y') }}</span>
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 rounded-full text-[10px] normal-case font-bold border border-gray-200" style="position: absolute; left: 50%; transform: translateX(-50%); white-space: nowrap;">
                                    {{ __('Durasi Total: ' . $projectDurationDays . ' Hari') }}
                                </span>
                                <span>{{ $maxDate->format('d M Y') }}</span>
                            </div>
                        </div>

                        <!-- Gantt Rows Container -->
                        <div class="mt-2 divide-y divide-gray-100">
                            @foreach($wbsItems as $wbs)
                                @include('project-planning.timeline._gantt_row', [
                                    'wbs' => $wbs, 
                                    'depth' => 0, 
                                    'projectDurationDays' => $projectDurationDays, 
                                    'minDate' => $minDate
                                ])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Toggle Vanilla JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabTableBtn = document.getElementById('tab-table-btn');
            const tabGanttBtn = document.getElementById('tab-gantt-btn');
            const tabTableContent = document.getElementById('tab-table-content');
            const tabGanttContent = document.getElementById('tab-gantt-content');

            if (tabTableBtn && tabGanttBtn && tabTableContent && tabGanttContent) {
                tabTableBtn.addEventListener('click', function () {
                    // Update buttons styling
                    tabTableBtn.classList.add('border-primary', 'text-primary');
                    tabTableBtn.classList.remove('border-transparent', 'text-secondaryText');
                    tabGanttBtn.classList.add('border-transparent', 'text-secondaryText');
                    tabGanttBtn.classList.remove('border-primary', 'text-primary');

                    // Toggle contents
                    tabTableContent.classList.remove('hidden');
                    tabTableContent.classList.add('block');
                    tabGanttContent.classList.remove('block');
                    tabGanttContent.classList.add('hidden');
                });

                tabGanttBtn.addEventListener('click', function () {
                    // Update buttons styling
                    tabGanttBtn.classList.add('border-primary', 'text-primary');
                    tabGanttBtn.classList.remove('border-transparent', 'text-secondaryText');
                    tabTableBtn.classList.add('border-transparent', 'text-secondaryText');
                    tabTableBtn.classList.remove('border-primary', 'text-primary');

                    // Toggle contents
                    tabGanttContent.classList.remove('hidden');
                    tabGanttContent.classList.add('block');
                    tabTableContent.classList.remove('block');
                    tabTableContent.classList.add('hidden');
                });
            }
        });
    </script>
</x-app-layout>
