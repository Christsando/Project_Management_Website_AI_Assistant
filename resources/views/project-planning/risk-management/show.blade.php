<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        $isDraft = $riskPlan && $riskPlan->status === 'draft';
        
        if (!function_exists('getInitials')) {
            function getInitials($name) {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
                return $initials ?: 'PM';
            }
        }

        // Decode AI Suggestions from riskPlan model
        $aiSuggestions = [];
        if ($riskPlan && $riskPlan->ai_suggestions) {
            try {
                $aiSuggestions = json_decode($riskPlan->ai_suggestions, true) ?: [];
            } catch (\Exception $e) {
                $aiSuggestions = [];
            }
        }

        // Calculate Project Health
        $healthPercent = $totalRisks > 0 ? round((($statusMitigated + $statusAccepted + $statusClosed) / $totalRisks) * 100) : 100;
        $healthText = 'Stabil. Sebagian besar risiko telah memiliki mitigasi aktif.';
        $healthTitle = 'Stabil';
        $healthColor = 'text-blue-600';
        $healthStroke = 'stroke-blue-600';
        if ($healthPercent >= 80) {
            $healthText = 'Sehat. Sebagian besar risiko telah memiliki mitigasi aktif.';
            $healthTitle = 'Sehat';
            $healthColor = 'text-emerald-500';
            $healthStroke = 'stroke-emerald-500';
        } elseif ($healthPercent < 50) {
            $healthText = 'Kritis. Segera lakukan penyusunan rencana mitigasi.';
            $healthTitle = 'Kritis';
            $healthColor = 'text-rose-500';
            $healthStroke = 'stroke-rose-500';
        }
        
        // SVG Circle Dash Calculations
        $radius = 12;
        $circumference = 2 * pi() * $radius;
        $strokeDashoffset = $circumference - ($healthPercent / 100) * $circumference;
    @endphp

    <div class="pl-4 pt-2 pb-12">
        <!-- Top Sub-Navigation Tabs -->
        <div class="flex items-center gap-6 border-b border-slate-100 mb-6 px-4">
            <a href="{{ route('projects.human-resource.show', $project->id) }}" class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Human Resource Planning') }}
            </a>
            <a href="{{ route('projects.timeline.show', $project->id) }}" class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Gantt Chart') }}
            </a>
            <a href="{{ route('projects.budget.show', $project->id) }}" class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Budgeting') }}
            </a>
            <a href="{{ route('projects.risk-management.show', $project->id) }}" class="pb-3 text-xs font-bold text-blue-600 border-b-2 border-blue-600 transition">
                {{ __('Risk Management') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.risk-management.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Rincian Rencana Manajemen Risiko') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Tinjau draf atau dokumen rencana manajemen risiko proyek secara keseluruhan.') }}
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
                        <a href="{{ route('projects.risk-management.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Rencana Risiko') }}
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

            <!-- Finalized status banner or Draft banner -->
            @if($riskPlan && $riskPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-emerald-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Perencanaan proyek selesai') }}</h4>
                            <p class="text-xs text-emerald-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Semua modul perencanaan telah diverifikasi. Proyek siap untuk tahap eksekusi.') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-emerald-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-sm transition gap-1.5 shrink-0">
                        {{ __('Lihat Detail') }}
                    </a>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Draf Rencana Manajemen Risiko (Belum Final)') }}</h4>
                            <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Rencana penanganan risiko proyek sedang disusun oleh PMO. Silakan hubungi PMO jika ingin menambahkan mitigasi.') }}
                            </p>
                        </div>
                    </div>
                    @if($isPmo && $riskItems->count() > 0)
                        <form action="{{ route('projects.risk-management.finalize', $project->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi rencana manajemen risiko ini?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Finalisasi Rencana Risiko') }}
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Plan content -->
            @if(!$riskPlan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-slate-800 mb-1">{{ __('Belum Ada Rencana Manajemen Risiko') }}</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">{{ __('Perencanaan risiko proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Three summary cards in a row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card 1: Total Risiko -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                {{ __('Total Risiko') }}
                            </span>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">
                                {{ sprintf('%02d', $totalRisks) }}
                            </h3>
                        </div>
                    </div>

                    <!-- Card 2: Probabilitas Tinggi -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                {{ __('Probabilitas Tinggi') }}
                            </span>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">
                                {{ sprintf('%02d', $probHigh) }}
                            </h3>
                        </div>
                    </div>

                    <!-- Card 3: Keparahan Tinggi -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">
                                {{ __('Keparahan Tinggi') }}
                            </span>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">
                                {{ sprintf('%02d', $sevHigh) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Main Layout Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Table and interactive details panel -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-slate-50 pb-4 mb-5 flex items-center justify-between">
                                <h4 class="font-bold text-base text-slate-800">{{ __('Daftar Risiko Proyek') }}</h4>
                            </div>

                            <!-- Table -->
                            @if($riskItems->isEmpty())
                                <div class="p-16 text-center">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-shield-alt text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Alokasi Risiko Kosong') }}</h5>
                                    <p class="text-xs text-slate-500">{{ __('Belum ada rincian alokasi potensi risiko pelaksana untuk proyek ini.') }}</p>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-slate-50">
                                            <thead>
                                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    <th class="py-3">{{ __('JUDUL RISIKO') }}</th>
                                                    <th class="py-3 px-3">{{ __('DAMPAK') }}</th>
                                                    <th class="py-3 px-3">{{ __('PROBABILITAS') }}</th>
                                                    <th class="py-3 px-3">{{ __('SEVERITY') }}</th>
                                                    <th class="py-3 px-3">{{ __('OWNER') }}</th>
                                                    <th class="py-3 text-right pr-2">{{ __('STATUS') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="risk-table-body" class="divide-y divide-slate-50 text-xs">
                                                @foreach($riskItems as $idx => $item)
                                                    @php
                                                        // Map impact color
                                                        $impactLabel = 'Sedang';
                                                        $impactColor = 'text-blue-600';
                                                        $sevVal = strtolower($item->severity);
                                                        $probVal = strtolower($item->probability);
                                                        
                                                        if ($sevVal === 'high' && $probVal === 'high') {
                                                            $impactLabel = 'Sangat Tinggi';
                                                            $impactColor = 'text-rose-700';
                                                        } elseif ($sevVal === 'high' || $probVal === 'high') {
                                                            $impactLabel = 'Tinggi';
                                                            $impactColor = 'text-rose-500';
                                                        } elseif ($sevVal === 'low' && $probVal === 'low') {
                                                            $impactLabel = 'Rendah';
                                                            $impactColor = 'text-emerald-500';
                                                        }
                                                        
                                                        // Map probability percentage
                                                        $probPercent = 20;
                                                        $probBarColor = 'bg-emerald-500';
                                                        if ($probVal === 'high') {
                                                            $probPercent = 80;
                                                            $probBarColor = 'bg-rose-500';
                                                        } elseif ($probVal === 'medium') {
                                                            $probPercent = 50;
                                                            $probBarColor = 'bg-blue-600';
                                                        }
                                                        
                                                        // Map severity badge
                                                        $sevText = 'Menengah';
                                                        $sevBadge = 'bg-[#E0F2FE] text-[#0284C7] border-[#BAE6FD]';
                                                        if ($sevVal === 'high') {
                                                            $sevText = 'Kritis';
                                                            $sevBadge = 'bg-[#FFE4E6] text-[#E11D48] border-[#FECDD3]';
                                                        } elseif ($sevVal === 'low') {
                                                            $sevText = 'Rendah';
                                                            $sevBadge = 'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]';
                                                        }
                                                        
                                                        // Map status badge
                                                        $statusText = 'Direncanakan';
                                                        $statusBadge = 'bg-slate-100 text-slate-700 border border-slate-200';
                                                        $stVal = strtolower($item->status);
                                                        if ($stVal === 'mitigated') {
                                                            $statusText = 'Mitigasi Aktif';
                                                            $statusBadge = 'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]';
                                                        } elseif ($stVal === 'accepted') {
                                                            $statusText = 'Diterima';
                                                            $statusBadge = 'bg-[#FEF3C7] text-[#D97706] border-[#FDE68A]';
                                                        } elseif ($stVal === 'closed') {
                                                            $statusText = 'Selesai';
                                                            $statusBadge = 'bg-slate-50 text-slate-500 border border-slate-200';
                                                        }
                                                        
                                                        // Dynamic category
                                                        $category = 'Operasional Proyek';
                                                        if ($item->wbsItem) {
                                                            $category = $item->wbsItem->title;
                                                        }
                                                    @endphp
                                                    <tr class="cursor-pointer hover:bg-slate-50/40 transition duration-150 {{ $idx === 0 ? 'bg-blue-50/20 border-l-4 border-blue-600' : '' }}"
                                                        onclick="showRiskDetails(this)"
                                                        data-mitigation="{{ $item->mitigation_plan }}"
                                                        data-contingency="{{ $item->contingency_plan ?: __('Tidak ada rencana kontingensi.') }}">
                                                        <td class="py-4 pr-3 max-w-[180px]">
                                                            <div class="font-bold text-slate-800 text-sm truncate" title="{{ $item->risk_title }}">
                                                                {{ $item->risk_title }}
                                                            </div>
                                                            <div class="text-[10px] text-slate-400 font-medium truncate mt-0.5" title="{{ $category }}">{{ $category }}</div>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <span class="font-bold {{ $impactColor }}">{{ $impactLabel }}</span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden mb-1">
                                                                <div class="h-full rounded-full {{ $probBarColor }} transition-all" style="width: {{ $probPercent }}%"></div>
                                                            </div>
                                                            <span class="text-[9px] font-bold text-slate-400 font-mono">{{ $probPercent }}%</span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $sevBadge }}">
                                                                {{ $sevText }}
                                                            </span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-[9px] shadow-sm shrink-0">
                                                                    {{ getInitials($item->risk_owner ?: 'PM') }}
                                                                </div>
                                                                <span class="font-bold text-slate-700 truncate max-w-[80px]">{{ $item->risk_owner ?: 'PM' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-4 text-right pr-2">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $statusBadge }}">
                                                                {{ $statusText }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- bottom details panel -->
                        @php
                            $firstItem = $riskItems->first();
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-[10px] uppercase text-slate-400 tracking-wider mb-4">{{ __('DETIL STRATEGI MITIGASI TERPILIH') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="font-bold text-xs text-slate-700 mb-1.5">{{ __('Rencana Mitigasi (Preventif)') }}</h5>
                                    <p id="detail-mitigation" class="text-xs text-slate-500 leading-relaxed font-semibold whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        {{ $firstItem ? $firstItem->mitigation_plan : __('Pilih baris risiko di atas untuk melihat detail mitigasi.') }}
                                    </p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xs text-slate-700 mb-1.5">{{ __('Rencana Kontingensi (Reaktif)') }}</h5>
                                    <p id="detail-contingency" class="text-xs text-slate-500 leading-relaxed font-semibold whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        {{ $firstItem ? ($firstItem->contingency_plan ?: __('Tidak ada rencana kontingensi.')) : __('Pilih baris risiko di atas untuk melihat detail kontingensi.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="space-y-6">
                        <!-- AI Recommendations Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-3 mb-4">
                                <h4 class="font-bold text-sm text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-robot text-blue-600"></i>
                                    {{ __('Rekomendasi AI') }}
                                </h4>
                            </div>

                            <!-- Suggestions List -->
                            @if(empty($aiSuggestions))
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-lightbulb text-lg"></i>
                                    </div>
                                    <p class="text-xs text-slate-400 italic px-4 leading-relaxed">{{ __('Belum ada rekomendasi AI.') }}</p>
                                </div>
                            @else
                                <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                                    @foreach($aiSuggestions as $idx => $sug)
                                        @php
                                            $category = 'Wawasan';
                                            $badgeColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                                            $sev = strtolower($sug['severity'] ?? 'medium');
                                            if ($sev === 'high') {
                                                $category = 'Peringatan';
                                                $badgeColor = 'bg-rose-50 text-rose-600 border border-rose-100';
                                            } elseif ($sev === 'medium') {
                                                $category = 'Efisiensi';
                                                $badgeColor = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                            }
                                        @endphp
                                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $badgeColor }}">
                                                    {{ $category }}
                                                </span>
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-800 leading-snug">{{ $sug['risk_title'] ?? '-' }}</h5>
                                            <p class="text-[10px] text-slate-500 leading-relaxed font-medium mt-1">{{ $sug['risk_description'] ?? '-' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Kesehatan Risiko Proyek Card (Radial Chart) -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <h4 class="font-bold text-[10px] uppercase text-slate-400 tracking-wider mb-4">{{ __('KESEHATAN RISIKO PROYEK') }}</h4>
                            <div class="flex items-center gap-4">
                                <!-- Radial chart -->
                                <div class="relative w-12 h-12 shrink-0 flex items-center justify-center">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <!-- Background circle -->
                                        <circle cx="18" cy="18" r="{{ $radius }}" fill="none" stroke="#F1F5F9" stroke-width="3"></circle>
                                        <!-- Progress circle -->
                                        <circle cx="18" cy="18" r="{{ $radius }}" fill="none" class="{{ $healthStroke }} transition-all duration-500" stroke-width="3" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $strokeDashoffset }}"></circle>
                                    </svg>
                                    <span class="absolute font-bold text-slate-800 text-[10px]">{{ $healthPercent }}%</span>
                                </div>
                                <div>
                                    <h5 class="font-extrabold text-xs {{ $healthColor }}">{{ $healthTitle }}</h5>
                                    <p class="text-[10px] text-slate-400 font-semibold leading-normal mt-0.5">
                                        {{ $healthText }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                {{ __('Catatan Rencana Risiko') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-relaxed whitespace-pre-line font-medium bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $riskPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JS script for interactive details display -->
    <script>
        function showRiskDetails(row) {
            const mitigation = row.getAttribute('data-mitigation');
            const contingency = row.getAttribute('data-contingency');
            
            document.getElementById('detail-mitigation').innerText = mitigation;
            document.getElementById('detail-contingency').innerText = contingency;
            
            // Highlight selected row styling
            const tbody = document.getElementById('risk-table-body');
            Array.from(tbody.children).forEach(r => {
                r.classList.remove('bg-blue-50/20', 'border-l-4', 'border-blue-600');
            });
            row.classList.add('bg-blue-50/20', 'border-l-4', 'border-blue-600');
        }
    </script>
</x-app-layout>
