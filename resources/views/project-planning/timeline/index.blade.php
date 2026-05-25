<x-app-layout>
    <x-slot name="header">
        <x-header-component :title="'Timeline & Gantt Chart'" icon="fa-solid fa-calendar-alt text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <!-- Header Section -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mb-1">{{ __('Timeline Planning & Gantt Chart') }}</h2>
                <p class="text-xs text-slate-500">
                    @if(strtolower(Auth::user()->role) === 'pmo' || strtolower(Auth::user()->role) === 'project management officer')
                        {{ __('Kelola jadwal pelaksanaan kerja proyek berdasarkan WBS yang sudah difinalisasi.') }}
                    @else
                        {{ __('Tinjau rencana jadwal waktu (timeline) dan visualisasi Gantt Chart.') }}
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('project-planning') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-800 font-bold rounded-xl text-xs transition shadow-sm gap-2">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    {{ __('Kembali ke Planning') }}
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

        <!-- List Projects in Planning Status -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            @if($projects->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 border border-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <h4 class="font-extrabold text-lg text-slate-800 mb-1">{{ __('Tidak ada proyek ditemukan') }}</h4>
                    <p class="text-xs text-slate-500">{{ __('Belum ada proyek dalam status Planning yang tersedia untuk Anda.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">{{ __('Nama Proyek') }}</th>
                                <th class="px-6 py-4">{{ __('Project Manager') }}</th>
                                <th class="px-6 py-4">{{ __('Status WBS') }}</th>
                                <th class="px-6 py-4">{{ __('Status Timeline') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @foreach($projects as $project)
                                @php
                                    $userRole = strtolower(Auth::user()->role);
                                    $isWbsFinalized = ($project->wbsItems->count() > 0 && $project->wbsItems->where('status', 'draft')->count() === 0);
                                    
                                    // Calculate Timeline status
                                    $totalTimeline = $project->timelineItems->count();
                                    $draftTimeline = $project->timelineItems->where('status', 'draft')->count();
                                    
                                    if (!$isWbsFinalized) {
                                        $timelineStatus = 'waiting_wbs';
                                    } elseif ($totalTimeline === 0) {
                                        $timelineStatus = 'none';
                                    } elseif ($draftTimeline === 0) {
                                        $timelineStatus = 'finalized';
                                    } else {
                                        $timelineStatus = 'draft';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 text-sm mb-0.5">{{ $project->title }}</div>
                                        <div class="text-[10px] text-slate-400">{{ __('Mulai: ') . ($project->start_date ? $project->start_date->format('d M Y') : '-') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-semibold">
                                        {{ $project->owner ? $project->owner->name : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($isWbsFinalized)
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ __('Finalized') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ __('Belum Final') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($timelineStatus === 'waiting_wbs')
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fas fa-clock text-[9px]"></i>
                                                {{ __('Menunggu WBS Final') }}
                                            </span>
                                        @elseif($timelineStatus === 'none')
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ __('Belum Dibuat') }}
                                            </span>
                                        @elseif($timelineStatus === 'draft')
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                {{ __('Draft') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ __('Finalized') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            @if($isWbsFinalized)
                                                @if($timelineStatus === 'none')
                                                    @if($userRole === 'pmo' || $userRole === 'project management officer')
                                                        <a href="{{ route('projects.timeline.create', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white shadow-sm transition">
                                                            <i class="fas fa-plus mr-1"></i> {{ __('Buat Timeline') }}
                                                        </a>
                                                    @else
                                                        <span class="text-[10px] text-slate-400 italic font-bold py-1.5 px-3 block">
                                                            {{ __('Belum dibuat PMO') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('projects.timeline.show', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-900 shadow-sm transition">
                                                        <i class="fas fa-eye mr-1 text-slate-400"></i> {{ __('Detail Timeline') }}
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-[10px] text-slate-400 italic font-bold py-1.5 px-3 block">
                                                    {{ __('Menunggu WBS') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
