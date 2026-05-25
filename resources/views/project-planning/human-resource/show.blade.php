<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        $isDraft = $hrPlan && $hrPlan->status === 'draft';
        
        function getInitials($name) {
            $words = explode(' ', trim($name));
            $initials = '';
            foreach ($words as $w) {
                $initials .= strtoupper(substr($w, 0, 1));
                if (strlen($initials) >= 2) break;
            }
            return $initials ?: 'PIC';
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
                        {{ __('Perencanaan Sumber Daya Manusia') }}
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
                    @if($isPmo && $isDraft)
                        <a href="{{ route('projects.human-resource.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Perencanaan') }}
                        </a>
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

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="font-medium">{{ session('info') }}</span>
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
                        <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST" class="shrink-0">
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
                    
                    <!-- Left Column: Summary and Notes -->
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

                        <!-- Notes Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                {{ __('Catatan Perencanaan SDM') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-relaxed whitespace-pre-line font-medium bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $hrPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Rincian Item (Read-only Table) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-slate-50 pb-4 mb-5 flex items-center justify-between">
                                <h4 class="font-bold text-base text-slate-800">{{ __('Daftar Rincian Kebutuhan SDM') }}</h4>
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
</x-app-layout>
