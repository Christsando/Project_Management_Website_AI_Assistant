<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $userRole = strtolower(Auth::user()->role);
        $isPmo = $userRole === 'pmo' || $userRole === 'project management officer';
        $isDraft = $riskPlan && $riskPlan->status === 'draft';

        if (!function_exists('getInitials')) {
            function getInitials($name)
            {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                    if (strlen($initials) >= 2) {
                        break;
                    }
                }
                return $initials ?: 'PM';
            }
        }

        // Calculate Project Health
        $healthPercent =
            $totalRisks > 0 ? round((($statusMitigated + $statusAccepted + $statusClosed) / $totalRisks) * 100) : 100;
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
            <a href="{{ route('projects.human-resource.show', $project->id) }}"
                class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Human Resource Planning') }}
            </a>
            <a href="{{ route('projects.timeline.show', $project->id) }}"
                class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Gantt Chart') }}
            </a>
            <a href="{{ route('projects.budget.show', $project->id) }}"
                class="pb-3 text-xs font-bold text-slate-400 hover:text-slate-600 transition">
                {{ __('Budgeting') }}
            </a>
            <a href="{{ route('projects.risk-management.show', $project->id) }}"
                class="pb-3 text-xs font-bold text-blue-600 border-b-2 border-blue-600 transition">
                {{ __('Risk Management') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.risk-management.index') }}"
                    class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div
                class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Kelola Risk Management Plan') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Identifikasi potensi risiko proyek, tentukan dampak, probabilitas, keparahan, dan rumuskan rencana mitigasi.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-400 font-medium">
                        <span>{{ __('Proyek:') }}</span>
                        <span
                            class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $project->title }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-slate-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>
                    @if ($riskItems->count() > 0)
                        <form action="{{ route('projects.risk-management.finalize', $project->id) }}" method="POST"
                            class="inline"
                            onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi rencana manajemen risiko ini? Setelah finalized, seluruh alokasi risiko dan rencana mitigasi akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Rencana Risiko') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Finalization Status Banner -->
            @if ($riskPlan && $riskPlan->status === 'finalized')
                <div
                    class="mb-6 p-5 rounded-2xl bg-emerald-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Perencanaan proyek selesai') }}</h4>
                            <p class="text-xs text-emerald-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Semua modul perencanaan telah diverifikasi. Proyek siap untuk tahap eksekusi.') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('projects.show', $project->id) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-white text-emerald-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-sm transition gap-1.5 shrink-0">
                        {{ __('Lihat Detail') }}
                    </a>
                </div>
            @else
                <div
                    class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Draf Rencana Manajemen Risiko (Belum Final)') }}</h4>
                            <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Rencana penanganan risiko proyek sedang disusun oleh PMO. Silakan tambahkan item risiko atau gunakan AI Assistant.') }}
                            </p>
                        </div>
                    </div>
                    @if ($isPmo && $riskItems->count() > 0)
                        <form action="{{ route('projects.risk-management.finalize', $project->id) }}" method="POST"
                            class="shrink-0"
                            onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi rencana manajemen risiko ini?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Finalisasi Rencana Risiko') }}
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Plan content -->
            @if (!$riskPlan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div
                        class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-slate-800 mb-1">{{ __('Belum Ada Rencana Manajemen Risiko') }}
                    </h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                        {{ __('Perencanaan risiko proyek belum diinisialisasi oleh PMO.') }}</p>
                </div>
            @else
                <!-- Three summary cards in a row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card 1: Total Risiko -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
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
                        <div
                            class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center text-xl shrink-0">
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
                        <div
                            class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shrink-0">
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

                    <!-- Left Column: Table and details panel -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-slate-50 pb-4 mb-5 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-base text-slate-800">{{ __('Daftar Risiko Proyek') }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1 font-medium">
                                        {{ __('Kebutuhan mitigasi, probabilitas, keparahan, dan pemilik risiko.') }}
                                    </p>
                                </div>
                                <button type="button" onclick="openAddModal()"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambah Risiko') }}
                                </button>
                            </div>

                            <!-- Table -->
                            @if ($riskItems->isEmpty())
                                <div class="p-16 text-center">
                                    <div
                                        class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-shield-alt text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Alokasi Risiko Kosong') }}
                                    </h5>
                                    <p class="text-xs text-slate-500 mb-4">
                                        {{ __('Rencana risiko Anda kosong. Tambahkan item risiko secara manual atau gunakan AI Assistant.') }}
                                    </p>
                                    <button type="button" onclick="openAddModal()"
                                        class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition gap-1.5 shadow-sm">
                                        <i class="fas fa-plus"></i>
                                        {{ __('Tambah Item Risiko') }}
                                    </button>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-slate-50">
                                            <thead>
                                                <tr
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    <th class="py-3">{{ __('JUDUL RISIKO') }}</th>
                                                    <th class="py-3 px-3">{{ __('DAMPAK') }}</th>
                                                    <th class="py-3 px-3">{{ __('PROBABILITAS') }}</th>
                                                    <th class="py-3 px-3">{{ __('SEVERITY') }}</th>
                                                    <th class="py-3 px-3">{{ __('OWNER') }}</th>
                                                    <th class="py-3 px-3">{{ __('STATUS') }}</th>
                                                    <th class="py-3 text-right pr-2">{{ __('AKSI') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="risk-table-body" class="divide-y divide-slate-50 text-xs">
                                                @foreach ($riskItems as $idx => $item)
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
                                                        $statusBadge =
                                                            'bg-slate-100 text-slate-700 border border-slate-200';
                                                        $stVal = strtolower($item->status);
                                                        if ($stVal === 'mitigated') {
                                                            $statusText = 'Mitigasi Aktif';
                                                            $statusBadge =
                                                                'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]';
                                                        } elseif ($stVal === 'accepted') {
                                                            $statusText = 'Diterima';
                                                            $statusBadge =
                                                                'bg-[#FEF3C7] text-[#D97706] border-[#FDE68A]';
                                                        } elseif ($stVal === 'closed') {
                                                            $statusText = 'Selesai';
                                                            $statusBadge =
                                                                'bg-slate-50 text-slate-500 border border-slate-200';
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
                                                        <td class="py-4 pr-3 max-w-[140px]">
                                                            <div class="font-bold text-slate-800 text-sm truncate"
                                                                title="{{ $item->risk_title }}">
                                                                {{ $item->risk_title }}
                                                            </div>
                                                            <div class="text-[10px] text-slate-400 font-medium truncate mt-0.5"
                                                                title="{{ $category }}">{{ $category }}</div>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <span
                                                                class="font-bold {{ $impactColor }}">{{ $impactLabel }}</span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div
                                                                class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden mb-1">
                                                                <div class="h-full rounded-full {{ $probBarColor }} transition-all"
                                                                    style="width: {{ $probPercent }}%"></div>
                                                            </div>
                                                            <span
                                                                class="text-[9px] font-bold text-slate-400 font-mono">{{ $probPercent }}%</span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $sevBadge }}">
                                                                {{ $sevText }}
                                                            </span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div class="flex items-center gap-2">
                                                                <div
                                                                    class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-[9px] shadow-sm shrink-0">
                                                                    {{ getInitials($item->risk_owner ?: 'PM') }}
                                                                </div>
                                                                <span
                                                                    class="font-bold text-slate-700 truncate max-w-[80px]">{{ $item->risk_owner ?: 'PM' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $statusBadge }}">
                                                                {{ $statusText }}
                                                            </span>
                                                        </td>
                                                        <td class="py-4 text-right pr-2">
                                                            <div class="inline-flex items-center gap-1.5">
                                                                <!-- Edit Button -->
                                                                <button type="button"
                                                                    onclick='openEditModalFromBtn(this, {!! json_encode($item) !!})'
                                                                    class="w-7 h-7 flex items-center justify-center text-amber-600 bg-amber-50 border border-amber-100 hover:bg-amber-600 hover:text-white rounded-lg shadow-sm transition"
                                                                    title="{{ __('Edit Item') }}">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <!-- Delete Button -->
                                                                <form
                                                                    action="{{ route('projects.risk-management.items.delete', [$project->id, $item->id]) }}"
                                                                    method="POST" class="inline"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus item risiko ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="w-7 h-7 flex items-center justify-center text-rose-600 bg-rose-50 border border-rose-100 hover:bg-rose-600 hover:text-white rounded-lg shadow-sm transition"
                                                                        title="{{ __('Hapus Item') }}">
                                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                                    </button>
                                                                </form>
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

                        <!-- bottom details panel -->
                        @php
                            $firstItem = $riskItems->first();
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-[10px] uppercase text-slate-400 tracking-wider mb-4">
                                {{ __('DETIL STRATEGI MITIGASI TERPILIH') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="font-bold text-xs text-slate-700 mb-1.5">
                                        {{ __('Rencana Mitigasi (Preventif)') }}</h5>
                                    <p id="detail-mitigation"
                                        class="text-xs text-slate-500 leading-relaxed font-semibold whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        {{ $firstItem ? $firstItem->mitigation_plan : __('Pilih baris risiko di atas untuk melihat detail mitigasi.') }}
                                    </p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xs text-slate-700 mb-1.5">
                                        {{ __('Rencana Kontingensi (Reaktif)') }}</h5>
                                    <p id="detail-contingency"
                                        class="text-xs text-slate-500 leading-relaxed font-semibold whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
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
                            @if (empty($aiSuggestions))
                                <div class="text-center py-8">
                                    <div
                                        class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-lightbulb text-lg"></i>
                                    </div>
                                    <p class="text-xs text-slate-400 italic px-4 leading-relaxed">
                                        {{ __('Belum ada rekomendasi AI. Klik tombol di bawah untuk menganalisis risiko proyek.') }}
                                    </p>
                                </div>
                            @else
                                <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                                    @foreach ($aiSuggestions as $idx => $sug)
                                        @php
                                            $category = 'Wawasan';
                                            $badgeColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                                            $sev = strtolower($sug['severity'] ?? 'medium');
                                            if ($sev === 'high') {
                                                $category = 'Peringatan';
                                                $badgeColor = 'bg-rose-50 text-rose-600 border border-rose-100';
                                            } elseif ($sev === 'medium') {
                                                $category = 'Efisiensi';
                                                $badgeColor =
                                                    'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                            }
                                        @endphp
                                        <div class="cursor-pointer p-4 bg-slate-50 border border-slate-100 hover:border-blue-500 hover:bg-slate-50/50 rounded-xl transition duration-200 group"
                                            data-title="{{ $sug['risk_title'] ?? '' }}"
                                            data-description="{{ $sug['risk_description'] ?? '' }}"
                                            data-cause="{{ $sug['risk_cause'] ?? '' }}"
                                            data-impact="{{ $sug['impact'] ?? '' }}"
                                            data-probability="{{ $sug['probability'] ?? 'medium' }}"
                                            data-severity="{{ $sug['severity'] ?? 'medium' }}"
                                            data-mitigation="{{ $sug['mitigation_plan'] ?? '' }}"
                                            data-contingency="{{ $sug['contingency_plan'] ?? '' }}"
                                            data-owner="{{ $sug['risk_owner'] ?? '' }}"
                                            onclick="applySuggestionFromBtn(this)">
                                            <div class="flex items-center justify-between mb-2">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $badgeColor }}">
                                                    {{ $category }}
                                                </span>
                                                <i
                                                    class="fas fa-arrow-right text-slate-400 group-hover:text-blue-500 text-xs transition duration-200"></i>
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-800 leading-snug">
                                                {{ $sug['risk_title'] ?? '-' }}</h5>
                                            <p class="text-[10px] text-slate-500 leading-relaxed font-medium mt-1">
                                                {{ $sug['risk_description'] ?? '-' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Dotted border trigger button -->
                            <form action="{{ route('projects.risk-management.generate_ai', $project->id) }}"
                                method="POST"
                                onsubmit="document.getElementById('ai-spinner').classList.remove('hidden'); document.getElementById('ai-btn-text').innerText='{{ __('Menganalisis...') }}';">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 bg-white border border-dashed border-slate-300 hover:border-blue-500 hover:text-blue-600 rounded-xl text-xs font-bold text-slate-500 transition duration-200 flex items-center justify-center gap-1.5 shadow-sm mt-4">
                                    <i id="ai-spinner" class="fas fa-sync fa-spin hidden"></i>
                                    <i class="fas fa-sync text-[10px]"></i>
                                    <span id="ai-btn-text">{{ __('Perbarui Analisis') }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- Kesehatan Risiko Proyek Card (Radial Chart) -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <h4 class="font-bold text-[10px] uppercase text-slate-400 tracking-wider mb-4">
                                {{ __('KESEHATAN RISIKO PROYEK') }}</h4>
                            <div class="flex items-center gap-4">
                                <!-- Radial chart -->
                                <div class="relative w-12 h-12 shrink-0 flex items-center justify-center">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <!-- Background circle -->
                                        <circle cx="18" cy="18" r="{{ $radius }}" fill="none"
                                            stroke="#F1F5F9" stroke-width="3"></circle>
                                        <!-- Progress circle -->
                                        <circle cx="18" cy="18" r="{{ $radius }}" fill="none"
                                            class="{{ $healthStroke }} transition-all duration-500" stroke-width="3"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $strokeDashoffset }}"></circle>
                                    </svg>
                                    <span
                                        class="absolute font-bold text-slate-800 text-[10px]">{{ $healthPercent }}%</span>
                                </div>
                                <div>
                                    <h5 class="font-extrabold text-xs {{ $healthColor }}">{{ $healthTitle }}</h5>
                                    <p class="text-[10px] text-slate-400 font-semibold leading-normal mt-0.5">
                                        {{ $healthText }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Form Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                {{ __('Catatan Rencana Risiko') }}
                            </h4>
                            <form action="{{ route('projects.risk-management.update', $project->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <textarea name="notes" rows="4"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 mb-3 placeholder-slate-400"
                                    placeholder="Masukkan catatan penanganan risiko... ">{{ old('notes', $riskPlan->notes) }}</textarea>
                                <button type="submit"
                                    class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    {{ __('Simpan Catatan') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: ADD RISK ITEM -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true"
                onclick="closeAddModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.risk-management.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-plus text-blue-600"></i>
                                {{ __('Tambah Potensi Risiko') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()"
                                class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Judul Risiko -->
                            <div>
                                <label for="add_risk_title"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Judul Risiko') }}</label>
                                <input type="text" name="risk_title" id="add_risk_title" required
                                    placeholder="Contoh: Keterlambatan Integrasi API Pihak Ketiga"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Deskripsi Risiko -->
                            <div>
                                <label for="add_risk_description"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Risiko') }}</label>
                                <textarea name="risk_description" id="add_risk_description" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Jelaskan detail mengenai skenario risiko..."></textarea>
                            </div>

                            <!-- Penyebab Risiko -->
                            <div>
                                <label for="add_risk_cause"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Faktor Penyebab (Cause) (Optional)') }}</label>
                                <textarea name="risk_cause" id="add_risk_cause" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Penyebab utama dari risiko tersebut..."></textarea>
                            </div>

                            <!-- Dampak -->
                            <div>
                                <label for="add_impact"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Dampak Risiko (Impact)') }}</label>
                                <textarea name="impact" id="add_impact" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Dampak yang dirasakan pada jadwal/biaya/tim jika terjadi..."></textarea>
                            </div>

                            <!-- Probability & Severity & Status (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="add_probability"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Probabilitas') }}</label>
                                    <select name="probability" id="add_probability" required
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add_severity"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keparahan') }}</label>
                                    <select name="severity" id="add_severity" required
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add_status"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Status') }}</label>
                                    <select name="status" id="add_status"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="open" selected>Open</option>
                                        <option value="mitigated">Mitigated</option>
                                        <option value="accepted">Accepted</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rencana Mitigasi -->
                            <div>
                                <label for="add_mitigation_plan"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Rencana Mitigasi (Preventif)') }}</label>
                                <textarea name="mitigation_plan" id="add_mitigation_plan" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Tindakan pencegahan sebelum risiko terjadi..."></textarea>
                            </div>

                            <!-- Rencana Kontingensi -->
                            <div>
                                <label for="add_contingency_plan"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Rencana Kontingensi (Reaktif) (Optional)') }}</label>
                                <textarea name="contingency_plan" id="add_contingency_plan" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Langkah pemulihan jika risiko benar-benar terjadi..."></textarea>
                            </div>

                            <!-- WBS Link & Owner (Grid) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="add_related_wbs_item_id"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Tautkan WBS (Optional)') }}</label>
                                    <select name="related_wbs_item_id" id="add_related_wbs_item_id"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="">-- {{ __('Tidak ditautkan') }} --</option>
                                        @foreach ($wbsItems as $wbs)
                                            <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="add_risk_owner"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Pemilik Risiko (Owner)') }}</label>
                                    <input type="text" name="risk_owner" id="add_risk_owner"
                                        placeholder="Contoh: Project Manager"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keterangan Lain (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                                    placeholder="Catatan internal..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            {{ __('Simpan Risiko') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT RISK ITEM -->
    <div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true"
                onclick="closeEditModal()"></div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl border border-slate-100 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Potensi Risiko') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()"
                                class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Judul Risiko -->
                            <div>
                                <label for="edit_risk_title"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Judul Risiko') }}</label>
                                <input type="text" name="risk_title" id="edit_risk_title" required
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                            </div>

                            <!-- Deskripsi Risiko -->
                            <div>
                                <label for="edit_risk_description"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Risiko') }}</label>
                                <textarea name="risk_description" id="edit_risk_description" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>

                            <!-- Penyebab Risiko -->
                            <div>
                                <label for="edit_risk_cause"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Faktor Penyebab (Cause) (Optional)') }}</label>
                                <textarea name="risk_cause" id="edit_risk_cause" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>

                            <!-- Dampak -->
                            <div>
                                <label for="edit_impact"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Dampak Risiko (Impact)') }}</label>
                                <textarea name="impact" id="edit_impact" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>

                            <!-- Probability & Severity & Status (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="edit_probability"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Probabilitas') }}</label>
                                    <select name="probability" id="edit_probability" required
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_severity"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keparahan') }}</label>
                                    <select name="severity" id="edit_severity" required
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_status"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Status') }}</label>
                                    <select name="status" id="edit_status"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="open">Open</option>
                                        <option value="mitigated">Mitigated</option>
                                        <option value="accepted">Accepted</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rencana Mitigasi -->
                            <div>
                                <label for="edit_mitigation_plan"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Rencana Mitigasi (Preventif)') }}</label>
                                <textarea name="mitigation_plan" id="edit_mitigation_plan" required rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>

                            <!-- Rencana Kontingensi -->
                            <div>
                                <label for="edit_contingency_plan"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Rencana Kontingensi (Reaktif) (Optional)') }}</label>
                                <textarea name="contingency_plan" id="edit_contingency_plan" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>

                            <!-- WBS Link & Owner (Grid) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="edit_related_wbs_item_id"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Tautkan WBS (Optional)') }}</label>
                                    <select name="related_wbs_item_id" id="edit_related_wbs_item_id"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50">
                                        <option value="">-- {{ __('Tidak ditautkan') }} --</option>
                                        @foreach ($wbsItems as $wbs)
                                            <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_risk_owner"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Pemilik Risiko (Owner)') }}</label>
                                    <input type="text" name="risk_owner" id="edit_risk_owner"
                                        class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Keterangan Lain (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS script for interactive details display and modals toggling -->
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

        function openAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditModalFromBtn(btn, item) {
            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-item-form');

            // Fill prefilled values
            document.getElementById('edit_risk_title').value = item.risk_title || '';
            document.getElementById('edit_risk_description').value = item.risk_description || '';
            document.getElementById('edit_risk_cause').value = item.risk_cause || '';
            document.getElementById('edit_impact').value = item.impact || '';
            document.getElementById('edit_probability').value = item.probability || 'medium';
            document.getElementById('edit_severity').value = item.severity || 'medium';
            document.getElementById('edit_status').value = item.status || 'open';
            document.getElementById('edit_mitigation_plan').value = item.mitigation_plan || '';
            document.getElementById('edit_contingency_plan').value = item.contingency_plan || '';
            document.getElementById('edit_related_wbs_item_id').value = item.related_wbs_item_id || '';
            document.getElementById('edit_risk_owner').value = item.risk_owner || '';
            document.getElementById('edit_notes').value = item.notes || '';

            // Update action route dynamically
            form.action = `/projects/{{ $project->id }}/risk-management/items/${item.id}`;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function applySuggestionFromBtn(card) {
            // Open add modal
            openAddModal();

            // Populate form values from card data attributes
            document.getElementById('add_risk_title').value = card.getAttribute('data-title') || '';
            document.getElementById('add_risk_description').value = card.getAttribute('data-description') || '';
            document.getElementById('add_risk_cause').value = card.getAttribute('data-cause') || '';
            document.getElementById('add_impact').value = card.getAttribute('data-impact') || '';
            document.getElementById('add_probability').value = card.getAttribute('data-probability') || 'medium';
            document.getElementById('add_severity').value = card.getAttribute('data-severity') || 'medium';
            document.getElementById('add_mitigation_plan').value = card.getAttribute('data-mitigation') || '';
            document.getElementById('add_contingency_plan').value = card.getAttribute('data-contingency') || '';
            document.getElementById('add_risk_owner').value = card.getAttribute('data-owner') || '';
        }
    </script>
</x-app-layout>
