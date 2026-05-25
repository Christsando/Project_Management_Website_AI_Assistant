<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-600"></i>
                        {{ __('Human Resource Planning') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        @if(strtolower(Auth::user()->role) === 'pmo' || strtolower(Auth::user()->role) === 'project management officer')
                            {{ __('Kelola perencanaan sumber daya manusia (SDM) dan alokasi tugas pekerjaan (WBS) berdasarkan anggaran yang sudah difinalisasi.') }}
                        @else
                            {{ __('Tinjau perencanaan sumber daya manusia (SDM) dan rincian alokasi tugas pekerjaan.') }}
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('project-planning') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-semibold rounded-xl text-xs transition shadow-sm gap-2">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        {{ __('Kembali ke Planning') }}
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500 text-sm"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            @endif

            <!-- List Projects Table -->
            <div class="bg-white rounded-xl border border-slate-100 overflow-hidden shadow-sm">
                @if($projects->isEmpty())
                    <div class="p-16 text-center">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 border border-blue-100/50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fa-solid fa-users text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1">{{ __('Tidak Ada Proyek') }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">{{ __('Belum ada proyek dalam status Planning yang aktif untuk penyusunan alokasi SDM.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">{{ __('Nama Proyek') }}</th>
                                    <th class="px-6 py-4">{{ __('Project Manager') }}</th>
                                    <th class="px-6 py-4">{{ __('Status Anggaran') }}</th>
                                    <th class="px-6 py-4">{{ __('Status SDM (HR)') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @foreach($projects as $project)
                                    @php
                                        $userRole = strtolower(Auth::user()->role);
                                        $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
                                        
                                        // Check Budget finalization status
                                        $isBudgetFinalized = ($project->budgetPlan && $project->budgetPlan->status === 'finalized');
                                        
                                        // HR plan status
                                        $hrPlan = $project->humanResourcePlan;
                                        $hrStatus = $hrPlan ? $hrPlan->status : 'none';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800 text-sm">{{ $project->title }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium mt-1 flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt text-slate-300"></i>
                                                {{ __('Mulai: ') . ($project->start_date ? $project->start_date->format('d M Y') : '-') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 font-semibold">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-[10px] shadow-sm">
                                                    {{ strtoupper(substr($project->owner ? $project->owner->name : 'PM', 0, 2)) }}
                                                </div>
                                                <span>{{ $project->owner ? $project->owner->name : '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($isBudgetFinalized)
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    {{ __('Finalized') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                                    {{ __('Belum Final') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(!$isBudgetFinalized)
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class="fas fa-clock text-[9px]"></i>
                                                    {{ __('Menunggu Anggaran Final') }}
                                                </span>
                                            @elseif($hrStatus === 'none')
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                                    {{ __('Belum Dibuat') }}
                                                </span>
                                            @elseif($hrStatus === 'draft')
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                                    {{ __('Draft') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    {{ __('Finalized') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex gap-2">
                                                @if($isBudgetFinalized)
                                                    @if($hrStatus === 'none')
                                                        @if($isPmo)
                                                            <a href="{{ route('projects.human-resource.create', $project->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-100 hover:bg-blue-600 hover:text-white rounded-xl shadow-sm transition gap-1">
                                                                <i class="fas fa-plus text-[9px]"></i> {{ __('Buat HR Plan') }}
                                                            </a>
                                                        @else
                                                            <span class="text-xs text-slate-400 italic font-medium py-1 px-3 block">
                                                                {{ __('Belum dibuat oleh PMO') }}
                                                            </span>
                                                        @endif
                                                    @elseif($hrStatus === 'draft')
                                                        @if($isPmo)
                                                            <a href="{{ route('projects.human-resource.edit', $project->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-100 hover:bg-amber-600 hover:text-white rounded-xl shadow-sm transition gap-1">
                                                                <i class="fas fa-edit text-[9px]"></i> {{ __('Kelola HR Plan') }}
                                                            </a>
                                                        @else
                                                            <a href="{{ route('projects.human-resource.show', $project->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition gap-1">
                                                                <i class="fas fa-eye text-[9px] text-slate-400"></i> {{ __('Detail HR Plan') }}
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('projects.human-resource.show', $project->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition gap-1">
                                                            <i class="fas fa-eye text-[9px] text-slate-400"></i> {{ __('Detail HR Plan') }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-slate-400 italic font-medium py-1 px-3 block">
                                                        {{ __('Menunggu Finalisasi Anggaran') }}
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
    </div>
</x-app-layout>
