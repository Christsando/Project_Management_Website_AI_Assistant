<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        $isDraft = $hrPlan && $hrPlan->status === 'draft';
    @endphp

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
                        {{ __('Rincian Perencanaan Sumber Daya Manusia (SDM)') }}
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
                    @if($isPmo && $isDraft)
                        <a href="{{ route('projects.human-resource.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-amber-500/10 transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Perencanaan') }}
                        </a>
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

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Finalized / Draft Banner -->
            @if($hrPlan && $hrPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-check-double text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('HR Plan Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            {{ __('Siap digunakan untuk Risk Management.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature text-gray-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf Perencanaan SDM (Belum Final)') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            {{ __('Rencana kebutuhan SDM masih berupa draf dan sedang disusun oleh PMO.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Plan content -->
            @if(!$hrPlan)
                <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Belum ada data perencanaan SDM') }}</h4>
                    <p class="text-sm text-secondaryText mb-4">{{ __('Perencanaan sumber daya manusia (SDM) proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Main Layout Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Summary and Notes -->
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

                        <!-- Notes Card -->
                        <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-primaryText mb-2 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-primary"></i>
                                {{ __('Catatan Perencanaan SDM') }}
                            </h4>
                            <p class="text-xs text-secondaryText leading-relaxed whitespace-pre-line font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $hrPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Rincian Item (Read-only Table) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-gray-100 pb-4 mb-4">
                                <h4 class="font-bold text-base text-primaryText">{{ __('Daftar Rincian Kebutuhan SDM') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Kebutuhan peran, kompetensi, jobdesk, PIC, dan beban kerja.') }}</p>
                            </div>

                            <!-- Table -->
                            @if($hrItems->isEmpty())
                                <div class="p-12 text-center">
                                    <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-users text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Alokasi SDM Kosong') }}</h5>
                                    <p class="text-xs text-secondaryText">{{ __('Belum ada rincian alokasi kebutuhan tim pelaksana untuk proyek ini.') }}</p>
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
                                                                <span class="text-gray-400 italic text-[10px]">{{ __('Belum Ada PIC') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-3.5 px-3 text-right font-semibold text-secondaryText">
                                                            <span class="font-bold text-primaryText block">{{ $item->quantity }} {{ __('Orang') }}</span>
                                                            @if($item->estimated_work_days)
                                                                <span class="text-[10px] text-secondaryText block mt-0.5 font-mono">{{ $item->estimated_work_days }} {{ __('Hari') }}</span>
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
