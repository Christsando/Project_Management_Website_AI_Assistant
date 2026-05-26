<x-app-layout>
    @php
        $user = Auth::user();
        $isPM = strtolower($user->role) === 'project manager';
        $isManager = strtolower($user->role) === 'manager';
        $isPMO = in_array(strtolower($user->role), ['pmo', 'project management officer']);
        
        $projectQuery = \App\Models\Project::query();
        if ($isPM) {
            $projectQuery->where('owner_id', $user->id);
        }
        
        $totalProjects = (clone $projectQuery)->count();
        $draftProjects = (clone $projectQuery)->where('status', 'draft')->count();
        $submittedProjects = (clone $projectQuery)->where('status', 'submitted')->count();
        $planningProjects = (clone $projectQuery)->whereIn('status', ['approved', 'planning'])->count();
        $completedProjects = (clone $projectQuery)->where('status', 'completed')->count();
        
        // Latest 3 projects for recent activity list
        $recentProjects = (clone $projectQuery)
            ->with(['owner', 'manager'])
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();
            
        // Calculate actions dynamically based on roles and project statuses
        $nextActions = [];
        
        // 1. Check if there are projects in 'submitted' status (needs review)
        if ($isManager) {
            $submittedForAction = \App\Models\Project::where('status', 'submitted')->get();
            foreach ($submittedForAction as $proj) {
                $nextActions[] = [
                    'title' => 'Review Proposal: ' . $proj->title,
                    'subtext' => 'Menunggu Persetujuan Anda',
                    'link' => route('projects.edit', $proj->id),
                    'action_text' => 'Tinjau Sekarang',
                    'color' => 'rose',
                    'icon' => 'fa-file-signature'
                ];
            }
        }
        
        // 2. Check if there are projects in planning status and scope is not finalized
        $planningForAction = \App\Models\Project::where('status', 'planning')->get();
        foreach ($planningForAction as $proj) {
            // Check scope
            if (!$proj->scope || strtolower($proj->scope->status) !== 'finalized') {
                if ($isManager) {
                    $nextActions[] = [
                        'title' => 'Finalisasi Scope: ' . $proj->title,
                        'subtext' => 'Tahap Perencanaan Scope',
                        'link' => route('projects.scope.show', $proj->id),
                        'action_text' => 'Lengkapi Scope',
                        'color' => 'blue',
                        'icon' => 'fa-compass'
                    ];
                }
            } 
            // Check WBS
            elseif (!$proj->wbsItems()->exists() || \App\Models\WbsItem::where('project_id', $proj->id)->where('status', 'draft')->exists()) {
                if ($isPMO) {
                    $nextActions[] = [
                        'title' => 'Susun WBS Proyek: ' . $proj->title,
                        'subtext' => 'Tugas/WBS Belum Selesai',
                        'link' => route('projects.wbs.show', $proj->id),
                        'action_text' => 'Kelola WBS',
                        'color' => 'amber',
                        'icon' => 'fa-sitemap'
                    ];
                }
            }
            // Check Timeline
            elseif (!$proj->timelineItems()->exists() || \App\Models\TimelineItem::where('project_id', $proj->id)->where('status', 'draft')->exists()) {
                if ($isPMO) {
                    $nextActions[] = [
                        'title' => 'Jadwalkan Timeline: ' . $proj->title,
                        'subtext' => 'Timeline Belum Final',
                        'link' => route('projects.timeline.show', $proj->id),
                        'action_text' => 'Buka Gantt Chart',
                        'color' => 'indigo',
                        'icon' => 'fa-calendar-days'
                    ];
                }
            }
        }
        
        // Pad with default items from the mockup if we have less than 3
        if (count($nextActions) < 3) {
            $mockDefaults = [
                [
                    'title' => 'Review Proposal IT Upgrade',
                    'subtext' => 'Tenggat: Besok, 14:00',
                    'link' => route('projects.index'),
                    'action_text' => 'Kerjakan Sekarang',
                    'color' => 'rose',
                    'icon' => 'fa-file-signature'
                ],
                [
                    'title' => 'Finalisasi Anggaran Q3',
                    'subtext' => 'Status: Menunggu Konfirmasi',
                    'link' => route('projects.index'),
                    'action_text' => 'Buka Detail',
                    'color' => 'blue',
                    'icon' => 'fa-wallet'
                ],
                [
                    'title' => 'Verifikasi Tim Proyek Logistik',
                    'subtext' => 'Permintaan Baru: 5 Anggota',
                    'link' => route('teamManagement'),
                    'action_text' => 'Setujui Semua',
                    'color' => 'emerald',
                    'icon' => 'fa-user-check'
                ]
            ];
            
            foreach ($mockDefaults as $mockItem) {
                if (count($nextActions) >= 3) break;
                // Avoid duplicating titles if already present
                $exists = false;
                foreach ($nextActions as $act) {
                    if ($act['title'] === $mockItem['title']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $nextActions[] = $mockItem;
                }
            }
        }
    @endphp

    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pt-2 pb-8 flex flex-col gap-4">
        <!-- Row 1: Welcome Banner & Quick Access -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Welcome Banner (2/3 width) -->
            <div class="lg:col-span-2 bg-[#1964D4] rounded-2xl p-7 text-white relative overflow-hidden flex flex-col justify-between min-h-[220px] shadow-sm">
                <!-- Graphic overlay -->
                <div class="absolute right-6 bottom-0 opacity-15 pointer-events-none">
                    <svg class="w-56 h-36 text-white" viewBox="0 0 100 100" fill="currentColor">
                        <rect x="10" y="55" width="8" height="45" rx="1.5"></rect>
                        <rect x="25" y="45" width="8" height="55" rx="1.5"></rect>
                        <rect x="40" y="30" width="8" height="70" rx="1.5"></rect>
                        <rect x="55" y="40" width="8" height="60" rx="1.5"></rect>
                        <rect x="70" y="20" width="8" height="80" rx="1.5"></rect>
                        <rect x="85" y="35" width="8" height="65" rx="1.5"></rect>
                    </svg>
                </div>
                
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        @if($isManager)
                            {{ __('Selamat Datang Kembali, Manajer!') }}
                        @elseif($isPM)
                            {{ __('Selamat Datang Kembali, Pengusul Proyek!') }}
                        @elseif($isPMO)
                            {{ __('Selamat Datang Kembali, PMO Perencana!') }}
                        @else
                            {{ __('Selamat Datang Kembali, ') . Auth::user()->name . '!' }}
                        @endif
                    </h1>
                    <p class="text-sm text-blue-100/90 mt-2.5 max-w-xl leading-relaxed">
                        @if($isManager)
                            {{ __('Ada ') . $submittedProjects . __(' permintaan baru yang butuh persetujuan Anda hari ini. Pastikan jadwal perencanaan tetap pada jalurnya.') }}
                        @elseif($isPM)
                            {{ __('Ada ') . $submittedProjects . __(' usulan proyek Anda yang sedang ditinjau. Pantau status persetujuan secara berkala.') }}
                        @elseif($isPMO)
                            {{ __('Ada ') . $planningProjects . __(' proyek aktif dalam fase perencanaan hari ini. Mulai kelola WBS, Timeline, dan SDM sekarang.') }}
                        @else
                            {{ __('Kelola dan pantau seluruh aktivitas proyek Anda dengan mudah dan efisien di portal KelolaIN.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-6 z-10">
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-blue-50 text-[#1964D4] font-bold text-xs rounded-xl shadow-md shadow-black/5 transition-all">
                        {{ __('Lihat Laporan Mingguan') }}
                    </a>
                </div>
            </div>

            <!-- Quick Access (1/3 width) -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">{{ __('Akses Cepat') }}</h2>
                    <div class="flex flex-col gap-3">
                        @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['project manager', 'manager']))
                        <a href="{{ route('projects.index') }}" class="flex items-center justify-between p-3.5 bg-slate-55 hover:bg-slate-50 rounded-xl transition-all border border-slate-100/70 hover:border-slate-200 group">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 text-blue-600 p-2.5 rounded-xl group-hover:scale-105 transition-all">
                                    <i class="fas fa-rocket text-sm"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ __('Inisiasi Proyek') }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        @endif

                        @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['manager', 'project management officer', 'pmo']))
                        <a href="{{ route('project-planning') }}" class="flex items-center justify-between p-3.5 bg-slate-55 hover:bg-slate-50 rounded-xl transition-all border border-slate-100/70 hover:border-slate-200 group">
                            <div class="flex items-center gap-3">
                                <div class="bg-emerald-50 text-emerald-600 p-2.5 rounded-xl group-hover:scale-105 transition-all">
                                    <i class="fa-regular fa-calendar text-sm"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ __('Perencanaan Proyek') }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Card 1: Total Proyek -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Total Proyek') }}</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-black text-blue-600 tracking-tight">{{ $totalProjects }}</span>
                    <i class="fa-regular fa-folder text-slate-300 text-lg"></i>
                </div>
            </div>

            <!-- Card 2: Draf -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Draf') }}</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-black text-slate-800 tracking-tight">{{ $draftProjects }}</span>
                    <i class="fa-regular fa-envelope text-slate-300 text-lg"></i>
                </div>
            </div>

            <!-- Card 3: Permintaan -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Permintaan') }}</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-black text-rose-600 tracking-tight">{{ $submittedProjects }}</span>
                    <i class="fa-regular fa-clipboard text-slate-300 text-lg"></i>
                </div>
            </div>

            <!-- Card 4: Perencanaan -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Perencanaan') }}</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-black text-indigo-600 tracking-tight">{{ $planningProjects }}</span>
                    <i class="fa-solid fa-chart-line text-slate-300 text-lg"></i>
                </div>
            </div>

            <!-- Card 5: Selesai -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm hover:scale-[1.01] transition-all duration-300 flex flex-col justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Selesai') }}</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-black text-emerald-600 tracking-tight">{{ $completedProjects }}</span>
                    <i class="fa-regular fa-circle-check text-slate-300 text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Row 3: Workflow Progress & Next Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Left: Workflow Progress (2/3 width) -->
            <div class="lg:col-span-2 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">{{ __('Progres Alur Kerja') }}</h2>
                    <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 bg-blue-600 rounded-full"></span>
                            <span>{{ __('Terencana') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full"></span>
                            <span>{{ __('Selesai') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Custom Bar Chart matching the reference image layout -->
                <div class="flex justify-around items-end h-52 px-4 pb-2 border-b border-slate-100">
                    <!-- Jan -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-40 w-14 bg-slate-100/70 rounded-xl relative overflow-hidden flex flex-col justify-end">
                            <div class="bg-blue-600 rounded-b-xl w-full" style="height: 70%;"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold">{{ __('Jan') }}</span>
                    </div>

                    <!-- Feb -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-40 w-14 bg-slate-100/70 rounded-xl relative overflow-hidden flex flex-col justify-end">
                            <div class="bg-blue-600 rounded-b-xl w-full" style="height: 50%;"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold">{{ __('Feb') }}</span>
                    </div>

                    <!-- Mar -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-40 w-14 bg-slate-100/70 rounded-xl relative overflow-hidden flex flex-col justify-end">
                            <div class="bg-blue-600 rounded-b-xl w-full" style="height: 85%;"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold">{{ __('Mar') }}</span>
                    </div>

                    <!-- Apr -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-40 w-14 bg-slate-100/70 rounded-xl relative overflow-hidden flex flex-col justify-end">
                            <div class="bg-blue-600 rounded-b-xl w-full" style="height: 65%;"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold">{{ __('Apr') }}</span>
                    </div>

                    <!-- Mei -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-40 w-14 bg-slate-100/70 rounded-xl relative overflow-hidden flex flex-col justify-end">
                            <!-- Blue Stack (top) -->
                            <div class="bg-blue-600 w-full" style="height: 60%;"></div>
                            <!-- Green Stack (bottom) -->
                            <div class="bg-emerald-600 rounded-b-xl w-full" style="height: 30%;"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold">{{ __('Mei') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Next Actions (1/3 width) -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-6">{{ __('Tindakan Selanjutnya') }}</h2>
                    <div class="flex flex-col gap-5">
                        @foreach(array_slice($nextActions, 0, 3) as $act)
                        <div class="flex items-start gap-4">
                            <!-- Icon circles -->
                            @if($act['color'] === 'rose')
                                <div class="bg-rose-50 text-rose-500 border border-rose-100 p-2.5 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas {{ $act['icon'] }} text-xs"></i>
                                </div>
                            @elseif($act['color'] === 'blue')
                                <div class="bg-blue-50 text-blue-500 border border-blue-100 p-2.5 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas {{ $act['icon'] }} text-xs"></i>
                                </div>
                            @elseif($act['color'] === 'emerald')
                                <div class="bg-emerald-50 text-emerald-500 border border-emerald-100 p-2.5 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas {{ $act['icon'] }} text-xs"></i>
                                </div>
                            @else
                                <div class="bg-indigo-50 text-indigo-500 border border-indigo-100 p-2.5 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas {{ $act['icon'] }} text-xs"></i>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-xs font-bold text-slate-850 leading-snug line-clamp-1" title="{{ $act['title'] }}">{{ $act['title'] }}</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $act['subtext'] }}</p>
                                <a href="{{ $act['link'] }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 mt-2 inline-block transition-colors hover:underline">
                                    {{ $act['action_text'] }}
                                </a>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="border-slate-50">
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Recent Activities -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">{{ __('Aktivitas Terbaru') }}</h2>
                <a href="{{ route('projects.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 transition-all">
                    <span>{{ __('Lihat Semua') }}</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <th class="py-3.5 px-6 border-b border-slate-100">{{ __('Proyek') }}</th>
                            <th class="py-3.5 px-6 border-b border-slate-100">{{ __('Pengguna') }}</th>
                            <th class="py-3.5 px-6 border-b border-slate-100">{{ __('Aktivitas') }}</th>
                            <th class="py-3.5 px-6 border-b border-slate-100">{{ __('Waktu') }}</th>
                            <th class="py-3.5 px-6 border-b border-slate-100">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @if($recentProjects->isEmpty())
                            <!-- Fallback Mock Rows matching reference image exactly if no database records -->
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800">{{ __('Revitalisasi Gudang A1') }}</div>
                                    <div class="text-[10px] text-slate-450 mt-0.5">{{ __('Logistik & Distribusi') }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center font-bold text-indigo-600 text-[10px]">
                                            {{ __('AS') }}
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ __('Andi Saputra') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500">{{ __('Mengunggah dokumen Rencana Kerja') }}</td>
                                <td class="py-4 px-6 text-slate-400">{{ __('2 jam yang lalu') }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold">
                                        {{ __('Selesai') }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800">{{ __('Migrasi Cloud Server') }}</div>
                                    <div class="text-[10px] text-slate-450 mt-0.5">{{ __('Infrastruktur IT') }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-[10px]">
                                            {{ __('BR') }}
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ __('Budi Raharjo') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500">{{ __('Mengubah anggaran operasional') }}</td>
                                <td class="py-4 px-6 text-slate-400">{{ __('4 jam yang lalu') }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-bold">
                                        {{ __('Dalam Review') }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-800">{{ __('Sistem HRIS Terpadu') }}</div>
                                    <div class="text-[10px] text-slate-450 mt-0.5">{{ __('Sumber Daya Manusia') }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center font-bold text-rose-600 text-[10px]">
                                            {{ __('DM') }}
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ __('Dina Mahendra') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-500">{{ __('Membuat draf inisiasi proyek baru') }}</td>
                                <td class="py-4 px-6 text-slate-400">{{ __('Kemarin, 16:45') }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-[10px] font-bold">
                                        {{ __('Draf') }}
                                    </span>
                                </td>
                            </tr>
                        @else
                            <!-- Render Dynamic Database projects -->
                            @foreach($recentProjects as $proj)
                                @php
                                    // Generate initials
                                    $name = $proj->owner ? $proj->owner->name : 'System';
                                    $words = explode(' ', $name);
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($name, 0, 2));
                                    }
                                    
                                    // Categories mapping based on project details
                                    $category = __('General Project');
                                    if (stripos($proj->title, 'it') !== false || stripos($proj->title, 'cloud') !== false || stripos($proj->title, 'software') !== false || stripos($proj->title, 'sistem') !== false) {
                                        $category = __('Infrastruktur IT');
                                    } elseif (stripos($proj->title, 'gudang') !== false || stripos($proj->title, 'logistik') !== false || stripos($proj->title, 'distribusi') !== false) {
                                        $category = __('Logistik & Distribusi');
                                    } elseif (stripos($proj->title, 'hr') !== false || stripos($proj->title, 'sdm') !== false || stripos($proj->title, 'human') !== false) {
                                        $category = __('Sumber Daya Manusia');
                                    }
                                    
                                    // Activity description mapping based on project status
                                    $activityText = __('Mengupdate status proyek');
                                    if ($proj->status === 'draft') {
                                        $activityText = __('Membuat draf inisiasi proyek baru');
                                    } elseif ($proj->status === 'submitted') {
                                        $activityText = __('Mengajukan usulan proyek untuk ditinjau');
                                    } elseif ($proj->status === 'approved') {
                                        $activityText = __('Menyetujui usulan proyek baru');
                                    } elseif ($proj->status === 'planning') {
                                        $activityText = __('Memperbarui dokumen perencanaan proyek');
                                    } elseif ($proj->status === 'completed') {
                                        $activityText = __('Menyelesaikan seluruh tahapan perencanaan');
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800">{{ $proj->title }}</div>
                                        <div class="text-[10px] text-slate-450 mt-0.5">{{ $category }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-[10px]">
                                                {{ $initials }}
                                            </div>
                                            <span class="font-semibold text-slate-700">{{ $name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500">{{ $activityText }}</td>
                                    <td class="py-4 px-6 text-slate-400">{{ $proj->updated_at->diffForHumans() }}</td>
                                    <td class="py-4 px-6">
                                        @if($proj->status === 'draft')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-[10px] font-bold">
                                                {{ __('Draf') }}
                                            </span>
                                        @elseif($proj->status === 'submitted')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-bold">
                                                {{ __('Dalam Review') }}
                                            </span>
                                        @elseif($proj->status === 'approved' || $proj->status === 'planning')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold">
                                                {{ __('Planning') }}
                                            </span>
                                        @elseif($proj->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold">
                                                {{ __('Selesai') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold">
                                                {{ ucfirst($proj->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
