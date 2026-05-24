<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight flex items-center gap-2">
                        <i class="fas fa-sitemap text-primary"></i>
                        {{ __('WBS (Work Breakdown Structure) Management') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        @if(strtolower(Auth::user()->role) === 'pmo' || strtolower(Auth::user()->role) === 'project management officer')
                            {{ __('Pecah ruang lingkup proyek yang telah difinalisasi menjadi struktur tugas hierarkis.') }}
                        @else
                            {{ __('Lihat pembagian kerja dan struktur WBS proyek.') }}
                        @endif
                    </h3>
                </div>
                <div>
                    <a href="{{ route('project-planning') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 font-semibold rounded-xl text-sm transition shadow-sm gap-2">
                        <i class="fas fa-arrow-left text-xs"></i>
                        {{ __('Kembali ke Planning') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- List Projects in Planning Status -->
            <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                @if($projects->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sitemap text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Tidak ada proyek ditemukan') }}</h4>
                        <p class="text-sm text-secondaryText">{{ __('Belum ada proyek dalam status Planning yang tersedia untuk Anda.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-[#e3e3e0] text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                    <th class="px-6 py-4">{{ __('Nama Proyek') }}</th>
                                    <th class="px-6 py-4">{{ __('Project Manager') }}</th>
                                    <th class="px-6 py-4">{{ __('Status Scope') }}</th>
                                    <th class="px-6 py-4">{{ __('Status WBS') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($projects as $project)
                                    @php
                                        $userRole = strtolower(Auth::user()->role);
                                        $isScopeFinalized = ($project->scope && $project->scope->status === 'finalized');
                                        
                                        // Calculate WBS status
                                        $totalWbs = $project->wbsItems->count();
                                        $draftWbs = $project->wbsItems->where('status', 'draft')->count();
                                        
                                        if (!$isScopeFinalized) {
                                            $wbsStatus = 'waiting_scope';
                                        } elseif ($totalWbs === 0) {
                                            $wbsStatus = 'none';
                                        } elseif ($draftWbs === 0) {
                                            $wbsStatus = 'finalized';
                                        } else {
                                            $wbsStatus = 'draft';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-primaryText">{{ $project->title }}</div>
                                            <div class="text-xs text-secondaryText mt-0.5">{{ __('Mulai: ') . ($project->start_date ? $project->start_date->format('d M Y') : '-') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-secondaryText font-medium">
                                            {{ $project->owner ? $project->owner->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($project->scope)
                                                @php
                                                    $scopeStatusClasses = [
                                                        'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                        'finalized' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    ][$project->scope->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold border {{ $scopeStatusClasses }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    {{ $project->scope->status === 'finalized' ? __('Finalized') : ucfirst($project->scope->status) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    {{ __('Belum Dibuat') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($wbsStatus === 'waiting_scope')
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                    <i class="fas fa-clock text-[10px]"></i>
                                                    {{ __('Menunggu Scope Final') }}
                                                </span>
                                            @elseif($wbsStatus === 'none')
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    {{ __('Belum Dibuat') }}
                                                </span>
                                            @elseif($wbsStatus === 'draft')
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                                    {{ __('Draft') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ __('Finalized') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex gap-2">
                                                @if($isScopeFinalized)
                                                    @if($wbsStatus === 'none')
                                                        @if($userRole === 'pmo' || $userRole === 'project management officer')
                                                            <a href="{{ route('projects.wbs.create', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white shadow-sm transition">
                                                                <i class="fas fa-plus mr-1.5"></i> {{ __('Buat WBS') }}
                                                            </a>
                                                        @else
                                                            <span class="text-xs text-gray-400 italic font-medium py-1.5 px-3 block">
                                                                {{ __('Belum dibuat oleh PMO') }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('projects.wbs.show', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 shadow-sm transition">
                                                            <i class="fas fa-eye mr-1.5 text-gray-400"></i> {{ __('Detail WBS') }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-400 italic font-medium py-1.5 px-3 block">
                                                        {{ __('Menunggu Finalisasi Scope') }}
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
