<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        $isDraft = $riskPlan && $riskPlan->status === 'draft';
    @endphp

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-cardSection rounded-xl p-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.risk-management.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Rincian Rencana Manajemen Risiko') }}
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
                        <a href="{{ route('projects.risk-management.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-amber-500/10 transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Rencana Risiko') }}
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
            @if($riskPlan && $riskPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-flag-checkered text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('Risk Management Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            {{ __('Perencanaan proyek selesai.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature text-gray-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf Rencana Manajemen Risiko (Belum Final)') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            {{ __('Rencana penanganan risiko proyek masih berupa draf dan sedang disusun oleh PMO.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Content -->
            @if(!$riskPlan)
                <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Belum ada rencana manajemen risiko') }}</h4>
                    <p class="text-sm text-secondaryText mb-4">{{ __('Perencanaan risiko proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Main Layout Flex Container (Sidebar Left, Content Right) -->
                <div class="flex flex-col lg:flex-row gap-6 w-full">
                    
                    <!-- Left: Notes & Stats Aggregates (Sidebar) -->
                    <div class="w-full lg:w-80 xl:w-96 shrink-0 space-y-6">
                        <!-- Total Risks Count -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-md shadow-blue-500/15 transition-all hover:scale-[1.02] duration-300">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-blue-100 text-[10px] font-bold uppercase tracking-wider block">{{ __('Total Risiko') }}</span>
                                    <span class="text-3xl font-extrabold block mt-2">{{ $totalRisks }}</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Summary Card -->
                        <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm space-y-4">
                            <h4 class="font-bold text-xs uppercase text-primaryText tracking-wider pb-2 border-b border-gray-100 flex items-center gap-1.5">
                                <i class="fas fa-chart-pie text-primary"></i>
                                {{ __('Distribusi Parameter') }}
                            </h4>

                            <!-- Probability Distributions -->
                            <div class="space-y-2 text-xs">
                                <span class="font-bold text-secondaryText block text-[10px] uppercase tracking-wider">{{ __('Probabilitas') }}</span>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Tinggi (High)</span>
                                    <span class="font-bold text-rose-600 font-mono bg-rose-50 px-2 py-0.5 rounded border border-rose-100">{{ $probHigh }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Sedang (Medium)</span>
                                    <span class="font-bold text-amber-600 font-mono bg-amber-50 px-2 py-0.5 rounded border border-amber-100">{{ $probMed }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Rendah (Low)</span>
                                    <span class="font-bold text-emerald-600 font-mono bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">{{ $probLow }}</span>
                                </div>
                            </div>

                            <!-- Severity Distributions -->
                            <div class="space-y-2 text-xs pt-3 border-t border-gray-100">
                                <span class="font-bold text-secondaryText block text-[10px] uppercase tracking-wider">{{ __('Keparahan (Severity)') }}</span>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Tinggi (High)</span>
                                    <span class="font-bold text-rose-600 font-mono bg-rose-50 px-2 py-0.5 rounded border border-rose-100">{{ $sevHigh }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Sedang (Medium)</span>
                                    <span class="font-bold text-orange-600 font-mono bg-orange-50 px-2 py-0.5 rounded border border-orange-100">{{ $sevMed }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Rendah (Low)</span>
                                    <span class="font-bold text-emerald-600 font-mono bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">{{ $sevLow }}</span>
                                </div>
                            </div>

                            <!-- Status Distributions -->
                            <div class="space-y-2 text-xs pt-3 border-t border-gray-100">
                                <span class="font-bold text-secondaryText block text-[10px] uppercase tracking-wider">{{ __('Status Penanganan') }}</span>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Open</span>
                                    <span class="font-bold text-blue-600 font-mono bg-blue-50 px-2 py-0.5 rounded border border-blue-100">{{ $statusOpen }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Mitigated</span>
                                    <span class="font-bold text-emerald-600 font-mono bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">{{ $statusMitigated }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Accepted</span>
                                    <span class="font-bold text-amber-600 font-mono bg-amber-50 px-2 py-0.5 rounded border border-amber-100">{{ $statusAccepted }}</span>
                                </div>
                                <div class="flex items-center justify-between font-medium">
                                    <span class="text-gray-500">Closed</span>
                                    <span class="font-bold text-gray-500 font-mono bg-gray-50 px-2 py-0.5 rounded border border-gray-200">{{ $statusClosed }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Card -->
                        <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm">
                            <h4 class="font-bold text-xs uppercase text-primaryText tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fas fa-sticky-note text-primary"></i>
                                {{ __('Catatan Rencana Risiko') }}
                            </h4>
                            <p class="text-xs text-secondaryText leading-relaxed whitespace-pre-line bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                                {{ $riskPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: Risk Items List -->
                    <div class="flex-1 min-w-0 space-y-6">
                        <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                            <div class="border-b border-gray-100 pb-4 mb-4">
                                <h4 class="font-bold text-base text-primaryText">{{ __('Daftar Rincian Risiko Teridentifikasi') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Kebutuhan mitigasi, probabilitas, keparahan, dan pemilik risiko.') }}</p>
                            </div>

                            @if($riskItems->isEmpty())
                                <div class="p-12 text-center">
                                    <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-shield-alt text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Alokasi Risiko Kosong') }}</h5>
                                    <p class="text-xs text-secondaryText">{{ __('Belum ada rincian alokasi potensi risiko pelaksana untuk proyek ini.') }}</p>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-gray-100 table-fixed">
                                            <thead>
                                                <tr class="text-xs font-bold text-secondaryText uppercase tracking-wider">
                                                    <th class="py-3.5 pr-4 w-1/3 min-w-[240px]">{{ __('Risiko & Penyebab') }}</th>
                                                    <th class="py-3.5 px-4 w-[130px]">{{ __('Parameter') }}</th>
                                                    <th class="py-3.5 px-4 w-1/3 min-w-[240px]">{{ __('Penanganan (Mitigasi/Kontingensi)') }}</th>
                                                    <th class="py-3.5 px-4 w-[160px] text-center">{{ __('WBS / Owner') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 text-xs">
                                                @foreach($riskItems as $item)
                                                    <tr class="hover:bg-gray-50/50 transition">
                                                        <td class="py-4 pr-4 align-top">
                                                            <div class="font-bold text-primaryText text-sm leading-tight">{{ $item->risk_title }}</div>
                                                            <div class="text-[11px] text-gray-500 mt-1.5 leading-relaxed" title="{{ $item->risk_description }}">{{ $item->risk_description }}</div>
                                                            @if($item->risk_cause)
                                                                <div class="text-[10px] text-secondaryText mt-2 bg-gray-50 p-2 rounded-lg border border-gray-100/50">
                                                                    <span class="font-bold text-[9px] uppercase tracking-wider text-gray-400 block mb-0.5">Penyebab:</span>
                                                                    {{ $item->risk_cause }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 px-4 align-top">
                                                            <div class="space-y-1.5">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-[9px] uppercase font-bold text-gray-400 w-8">Prob:</span>
                                                                    @if($item->probability === 'high')
                                                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md text-[9px] font-bold">High</span>
                                                                    @elseif($item->probability === 'medium')
                                                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md text-[9px] font-bold">Medium</span>
                                                                    @else
                                                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[9px] font-bold">Low</span>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-[9px] uppercase font-bold text-gray-400 w-8">Sev:</span>
                                                                    @if($item->severity === 'high')
                                                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md text-[9px] font-bold">High</span>
                                                                    @elseif($item->severity === 'medium')
                                                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md text-[9px] font-bold">Medium</span>
                                                                    @else
                                                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[9px] font-bold">Low</span>
                                                                    @endif
                                                                </div>
                                                                <div class="pt-1.5">
                                                                    @if($item->status === 'open')
                                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Open</span>
                                                                    @elseif($item->status === 'mitigated')
                                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Mitigated</span>
                                                                    @elseif($item->status === 'accepted')
                                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Accepted</span>
                                                                    @else
                                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-700 border border-gray-200">Closed</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="py-4 px-4 align-top space-y-2">
                                                            <div class="text-[11px] leading-relaxed bg-emerald-50/30 p-2.5 rounded-lg border border-emerald-100/50">
                                                                <span class="font-bold text-[9px] text-emerald-800 uppercase tracking-wider block mb-1">Mitigasi (Preventif):</span>
                                                                {{ $item->mitigation_plan }}
                                                            </div>
                                                            @if($item->contingency_plan)
                                                                <div class="text-[11px] leading-relaxed bg-blue-50/30 p-2.5 rounded-lg border border-blue-100/50">
                                                                    <span class="font-bold text-[9px] text-blue-800 uppercase tracking-wider block mb-1">Kontingensi (Reaktif):</span>
                                                                    {{ $item->contingency_plan }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 px-4 align-top text-center">
                                                            @if($item->wbsItem)
                                                                <div class="font-bold text-primaryText text-xs truncate max-w-[140px] mx-auto bg-gray-50 border border-gray-100 rounded-lg p-1.5" title="{{ $item->wbsItem->title }}">
                                                                    {{ $item->wbsItem->title }}
                                                                </div>
                                                                <div class="text-[9px] text-gray-400 font-mono mt-1">WBS ID: #{{ $item->related_wbs_item_id }}</div>
                                                            @else
                                                                    <span class="text-gray-400 italic text-[10px] block">-</span>
                                                            @endif
                                                            <div class="text-[10px] text-secondaryText font-bold mt-2">
                                                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>{{ $item->risk_owner ?: __('Tanpa Owner') }}
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
</x-app-layout>
