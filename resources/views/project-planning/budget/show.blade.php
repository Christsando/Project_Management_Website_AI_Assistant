<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $categories = [
            'human_resource' => ['label' => 'SDM', 'color' => 'blue', 'icon' => 'fa-users', 'bg' => 'bg-[#E0F2FE] text-[#0284C7] border-[#BAE6FD]', 'hex' => '#0284c7'],
            'infrastructure' => ['label' => 'INFRASTRUKTUR', 'color' => 'purple', 'icon' => 'fa-server', 'bg' => 'bg-[#F3E8FF] text-[#7E22CE] border-[#E9D5FF]', 'hex' => '#7e22ce'],
            'tools' => ['label' => 'ALAT', 'color' => 'green', 'icon' => 'fa-laptop-code', 'bg' => 'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]', 'hex' => '#15803d'],
            'operational' => ['label' => 'OPERASIONAL', 'color' => 'rose', 'icon' => 'fa-route', 'bg' => 'bg-[#FFE4E6] text-[#E11D48] border-[#FECDD3]', 'hex' => '#e11d48'],
            'contingency' => ['label' => 'CADANGAN', 'color' => 'amber', 'icon' => 'fa-shield-alt', 'bg' => 'bg-[#FEF3C7] text-[#D97706] border-[#FDE68A]', 'hex' => '#d97706'],
            'other' => ['label' => 'LAIN-LAIN', 'color' => 'gray', 'icon' => 'fa-box', 'bg' => 'bg-[#F3F4F6] text-[#4B5563] border-[#E5E7EB]', 'hex' => '#4b5563'],
        ];
        
        $userRole = strtolower(Auth::user()->role);
        $isManager = ($userRole === 'manager');
        $isDraft = $budgetPlan && $budgetPlan->status === 'draft';
    @endphp

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Perencanaan Anggaran (RAB)') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Kelola alokasi dana proyek secara presisi dan transparan.') }}
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
                    @if($isManager && $isDraft)
                        <a href="{{ route('projects.budget.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Anggaran') }}
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

            <!-- Banner status -->
            @if($budgetPlan && $budgetPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">{{ __('Siap digunakan untuk Human Resource Planning') }}</h4>
                            <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                                {{ __('Sistem kini terintegrasi dengan modul SDM untuk estimasi biaya tenaga kerja otomatis berdasarkan role dan durasi proyek.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-slate-50 border border-slate-200/60 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-xl border border-slate-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">{{ __('Draf Anggaran (Belum Final)') }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ __('Rencana anggaran belanja masih berupa draf dan sedang disusun oleh Manager.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Plan content -->
            @if(!$budgetPlan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-slate-800 mb-1">{{ __('Belum Ada Anggaran') }}</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">{{ __('Rencana anggaran belanja (RAB) proyek belum diinisialisasi oleh Manager.') }}</p>
                </div>
            @else
                <!-- Main Layout Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Summary and Notes -->
                    <div class="space-y-6">
                        <!-- Total Budget Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between min-h-[140px] relative overflow-hidden">
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block flex items-center gap-1.5">
                                    <i class="fa-solid fa-wallet text-blue-600"></i>
                                    {{ __('Total Anggaran Proyek') }}
                                </span>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-3 tracking-tight">
                                    Rp {{ number_format($budgetPlan->total_budget, 0, ',', '.') }}
                                </h3>
                            </div>
                            <div class="mt-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#DCFCE7] text-[#15803D]">
                                    <i class="fa-solid fa-check text-[9px]"></i>
                                    {{ __('Sesuai dengan pagu anggaran') }}
                                </span>
                            </div>
                        </div>

                        <!-- Category Distribution -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-5 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-blue-600"></i>
                                {{ __('Distribusi Kategori') }}
                            </h4>

                            @php
                                $totalBudget = $budgetPlan->total_budget ?: 1;
                                $accumulatedPercent = 0;
                                $circumference = 251.2; // 2 * pi * 40
                                $svgCircles = [];
                                
                                foreach($categories as $key => $cat) {
                                    $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                    $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                    
                                    if ($percent > 0) {
                                        $dashArray = ($percent / 100) * $circumference;
                                        $dashOffset = -($accumulatedPercent / 100) * $circumference;
                                        $accumulatedPercent += $percent;
                                        
                                        $svgCircles[] = [
                                            'dash' => "$dashArray $circumference",
                                            'offset' => $dashOffset,
                                            'color' => $cat['hex'],
                                            'label' => $cat['label'],
                                            'percent' => round($percent)
                                        ];
                                    }
                                }
                            @endphp

                            <div class="relative flex items-center justify-center mb-6">
                                <div class="w-36 h-36">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <!-- Background Circle -->
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f1f5f9" stroke-width="10" />
                                        @if(empty($svgCircles))
                                            <circle cx="50" cy="50" r="40" fill="transparent" stroke="#cbd5e1" stroke-width="10" />
                                        @else
                                            @foreach($svgCircles as $circle)
                                                <circle cx="50" cy="50" r="40" fill="transparent" 
                                                        stroke="{{ $circle['color'] }}" 
                                                        stroke-width="10" 
                                                        stroke-dasharray="{{ $circle['dash'] }}" 
                                                        stroke-dashoffset="{{ $circle['offset'] }}"
                                                        class="transition-all duration-300 hover:stroke-[12px] cursor-pointer" />
                                            @endforeach
                                        @endif
                                    </svg>
                                </div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ __('Total') }}</span>
                                    <span class="text-base font-extrabold text-slate-800">100%</span>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="space-y-3.5 mt-4">
                                @foreach($categories as $key => $cat)
                                    @php
                                        $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                        $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $cat['hex'] }}"></span>
                                            {{ $cat['label'] }}
                                        </span>
                                        <span class="text-slate-800 font-mono">{{ round($percent) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- KelolaIN Academy Card -->
                        <div class="relative rounded-2xl overflow-hidden h-[130px] shadow-sm group">
                            <img src="/images/kelolain_academy.png" alt="KelolaIN Academy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/50 to-transparent flex flex-col justify-end p-4">
                                <p class="text-xs font-bold text-white leading-snug">
                                    {{ __('Pelajari panduan efisiensi anggaran korporat di KelolaIN Academy →') }}
                                </p>
                            </div>
                        </div>

                        <!-- Notes Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                {{ __('Catatan Rencana Anggaran') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-relaxed whitespace-pre-line font-medium bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $budgetPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Rincian Item (Read-only Table) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-slate-50 pb-4 mb-4">
                                <h4 class="font-bold text-base text-slate-800">{{ __('Rincian Item Anggaran') }}</h4>
                            </div>

                            <!-- Table -->
                            @if($budgetItems->isEmpty())
                                <div class="p-16 text-center">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-wallet text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Item Anggaran Kosong') }}</h5>
                                    <p class="text-xs text-slate-500">{{ __('Belum ada rincian alokasi dana belanja untuk proyek ini.') }}</p>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-slate-50">
                                            <thead>
                                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    <th class="py-3">{{ __('KATEGORI') }}</th>
                                                    <th class="py-3 px-3">{{ __('DESKRIPSI') }}</th>
                                                    <th class="py-3 text-center">{{ __('QTY') }}</th>
                                                    <th class="py-3 text-center">{{ __('SATUAN') }}</th>
                                                    <th class="py-3 text-right">{{ __('HARGA SATUAN') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50 text-xs">
                                                @foreach($budgetItems as $item)
                                                    @php
                                                        $catConfig = $categories[$item->category] ?? $categories['other'];
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/30 transition duration-150">
                                                        <td class="py-4 pr-3">
                                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $catConfig['bg'] }}">
                                                                {{ $catConfig['label'] }}
                                                            </span>
                                                        </td>
                                                        <td class="py-4 px-3">
                                                            <div class="font-bold text-slate-800 text-sm">{{ $item->description }}</div>
                                                            @if($item->notes)
                                                                <div class="text-[10px] text-slate-400 italic mt-1.5 flex items-center gap-1">
                                                                    <i class="far fa-comment"></i>
                                                                    {{ $item->notes }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="py-4 text-center font-bold text-slate-700">
                                                            {{ $item->quantity }}
                                                        </td>
                                                        <td class="py-4 text-center font-semibold text-slate-400">
                                                            {{ $item->unit }}
                                                        </td>
                                                        <td class="py-4 text-right">
                                                            <div class="font-bold text-slate-800 text-sm">
                                                                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                                            </div>
                                                            <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                                                {{ __('Total: ') }}Rp {{ number_format($item->total_cost, 0, ',', '.') }}
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

                        <!-- SDM Active Integration Alert Banner -->
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-600/10 text-blue-600 rounded-xl flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-slate-800">{{ __('Integrasi SDM Aktif') }}</h5>
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                    {{ __('Budget ini telah terhubung dengan daftar personil proyek. Setiap perubahan pada durasi kontrak di modul SDM akan secara otomatis memperbarui nominal anggaran SDM di atas.') }}
                                </p>
                                <a href="{{ route('projects.human-resource.show', $project->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 mt-3 transition">
                                    {{ __('Lihat Detail Personil Proyek') }}
                                    <i class="fas fa-external-link-alt text-[9px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
