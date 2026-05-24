<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $categories = [
            'human_resource' => ['label' => 'Sumber Daya Manusia', 'color' => 'blue', 'icon' => 'fa-users', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'infrastructure' => ['label' => 'Infrastruktur', 'color' => 'purple', 'icon' => 'fa-server', 'bg' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'tools' => ['label' => 'Software & Tools', 'color' => 'indigo', 'icon' => 'fa-laptop-code', 'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'operational' => ['label' => 'Operasional', 'color' => 'amber', 'icon' => 'fa-route', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'contingency' => ['label' => 'Biaya Cadangan', 'color' => 'rose', 'icon' => 'fa-shield-alt', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'other' => ['label' => 'Lain-lain', 'color' => 'gray', 'icon' => 'fa-box', 'bg' => 'bg-gray-50 text-gray-700 border-gray-200'],
        ];
        
        $userRole = strtolower(Auth::user()->role);
        $isManager = ($userRole === 'manager');
        $isDraft = $budgetPlan && $budgetPlan->status === 'draft';
    @endphp

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-cardSection rounded-xl p-6 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Rincian Rencana Anggaran Belanja (RAB)') }}
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
                    @if($isManager && $isDraft)
                        <a href="{{ route('projects.budget.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-amber-500/10 transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Kelola Anggaran') }}
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
            @if($budgetPlan && $budgetPlan->status === 'finalized')
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-check-double text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('Budget Plan Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            {{ __('Siap digunakan untuk Human Resource Planning.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature text-gray-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf Anggaran (Belum Final)') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            {{ __('Rencana anggaran belanja masih berupa draf dan sedang disusun oleh Manager.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Plan content -->
            @if(!$budgetPlan)
                <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Belum ada data anggaran') }}</h4>
                    <p class="text-sm text-secondaryText mb-4">{{ __('Rencana anggaran belanja (RAB) proyek belum diinisialisasi oleh Manager.') }}</p>
                </div>
            @else
                <!-- Main Layout Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Summary and Notes -->
                    <div class="space-y-6">
                        <!-- Total Budget Card -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/15">
                            <span class="text-blue-100 text-xs font-semibold uppercase tracking-wider">{{ __('Total Anggaran RAB') }}</span>
                            <h3 class="text-3xl font-extrabold mt-1.5 leading-none">
                                Rp {{ number_format($budgetPlan->total_budget, 0, ',', '.') }}
                            </h3>
                            <p class="text-[10px] text-blue-200 mt-2 font-medium">
                                <i class="fas fa-calculator mr-1"></i>
                                {{ __('Dihitung otomatis dari total seluruh item anggaran') }}
                            </p>
                        </div>

                        <!-- Category Distribution -->
                        <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-primaryText mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-primary"></i>
                                {{ __('Distribusi Anggaran') }}
                            </h4>
                            <div class="space-y-4">
                                @foreach($categories as $key => $cat)
                                    @php
                                        $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                        $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span class="text-secondaryText flex items-center gap-1.5">
                                                <i class="fas {{ $cat['icon'] }} w-4 text-center text-primary/70"></i>
                                                {{ $cat['label'] }}
                                            </span>
                                            <span class="text-primaryText font-mono">Rp {{ number_format($catSum, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full bg-{{ $cat['color'] }}-500 transition-all" style="width: {{ $percent }}%; background-color: currentColor; color: var(--color-{{ $cat['color'] }}-500, rgb(59, 130, 246));"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Notes Card -->
                        <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                            <h4 class="font-bold text-sm text-primaryText mb-2 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-primary"></i>
                                {{ __('Catatan Rencana Anggaran') }}
                            </h4>
                            <p class="text-xs text-secondaryText leading-relaxed whitespace-pre-line font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $budgetPlan->notes ?: __('Tidak ada catatan khusus.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Rincian Item (Read-only Table) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                            <!-- Section Title -->
                            <div class="border-b border-gray-100 pb-4 mb-4">
                                <h4 class="font-bold text-base text-primaryText">{{ __('Daftar Rincian Anggaran') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Rincian alokasi belanja yang telah diajukan.') }}</p>
                            </div>

                            <!-- Table -->
                            @if($budgetItems->isEmpty())
                                <div class="p-12 text-center">
                                    <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-wallet text-xl"></i>
                                    </div>
                                    <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Item Anggaran Kosong') }}</h5>
                                    <p class="text-xs text-secondaryText">{{ __('Belum ada rincian alokasi dana belanja untuk proyek ini.') }}</p>
                                </div>
                            @else
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle px-6">
                                        <table class="min-w-full text-left divide-y divide-gray-100">
                                            <thead>
                                                <tr class="text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                                    <th class="py-3">{{ __('Kategori & Deskripsi') }}</th>
                                                    <th class="py-3 text-center">{{ __('Qty') }}</th>
                                                    <th class="py-3 text-right">{{ __('Biaya Satuan') }}</th>
                                                    <th class="py-3 text-right">{{ __('Total Biaya') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 text-xs">
                                                @foreach($budgetItems as $item)
                                                    @php
                                                        $catConfig = $categories[$item->category] ?? $categories['other'];
                                                    @endphp
                                                    <tr class="hover:bg-gray-50/50 transition">
                                                        <td class="py-3.5 pr-3">
                                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $catConfig['bg'] }} mb-1">
                                                                <i class="fas {{ $catConfig['icon'] }} text-[8px]"></i>
                                                                {{ $catConfig['label'] }}
                                                            </div>
                                                            <div class="font-bold text-primaryText text-sm">{{ $item->description }}</div>
                                                            @if($item->notes)
                                                                <div class="text-[10px] text-gray-400 italic mt-0.5"><i class="far fa-comment mr-1"></i>{{ $item->notes }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="py-3.5 px-3 text-center font-semibold text-secondaryText">
                                                            {{ $item->quantity }} <span class="text-[10px] text-gray-400 font-normal ml-0.5">{{ $item->unit }}</span>
                                                        </td>
                                                        <td class="py-3.5 px-3 text-right font-mono text-secondaryText">
                                                            Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                                        </td>
                                                        <td class="py-3.5 px-3 text-right font-mono font-bold text-primaryText">
                                                            Rp {{ number_format($item->total_cost, 0, ',', '.') }}
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
