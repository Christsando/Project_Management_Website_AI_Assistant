<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Detail Proyek') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Informasi lengkap mengenai draf dan status perencanaan proyek.') }}
                </h3>
            </div>

            <!-- Detail Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm space-y-6">
                <!-- Title & Status -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-gray-100 pb-6">
                    <div>
                        <h1 class="text-xl font-bold text-primaryText">{{ $project->title }}</h1>
                        <p class="text-xs text-secondaryText mt-1">ID Proyek: #{{ $project->id }}</p>
                    </div>
                    <div>
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                'submitted' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                'planning' => 'bg-blue-50 text-blue-800 border-blue-200',
                            ][$project->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                </div>

                <!-- Project Proposal Hub Card -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50/80 via-indigo-50/30 to-white border border-blue-100/60 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50">
                            <i class="fas fa-file-contract text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-primaryText">{{ __('Project Proposal') }}</h4>
                            <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                                @if($project->proposal)
                                    @php
                                        $proposalStatusClasses = [
                                            'draft' => 'text-gray-600 font-semibold bg-gray-100/80 px-2 py-0.5 rounded-lg border border-gray-200/60',
                                            'submitted' => 'text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60',
                                        ][$project->proposal->status] ?? 'text-gray-600';
                                    @endphp
                                    {{ __('Proposal sudah dibuat. Status:') }} 
                                    <span class="inline-block {{ $proposalStatusClasses }}">
                                        {{ $project->proposal->status === 'submitted' ? __('Finalized') : ucfirst($project->proposal->status) }}
                                    </span>
                                @else
                                    {{ __('Proposal belum dibuat untuk proyek ini.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        @if(!$project->proposal && strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                            <a href="{{ route('projects.proposal.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-2">
                                <i class="fas fa-plus text-[10px]"></i>
                                {{ __('Buat Proposal') }}
                            </a>
                        @elseif($project->proposal)
                            <a href="{{ route('projects.proposal.show', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-2">
                                <i class="fas fa-eye text-gray-400"></i>
                                {{ __('Lihat Proposal') }}
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Project Charter Hub Card -->
                @php
                    $userRole = strtolower(Auth::user()->role);
                    $showCharterCard = true;
                    if (in_array($userRole, ['pmo', 'project management officer'])) {
                        $showCharterCard = ($project->status === 'planning');
                    }
                @endphp

                @if($showCharterCard)
                    <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-50/80 via-blue-50/30 to-white border border-indigo-100/60 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-indigo-600/10 text-indigo-600 rounded-2xl border border-indigo-200/50">
                                <i class="fas fa-file-signature text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-primaryText">{{ __('Project Charter') }}</h4>
                                <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                                    @if($project->charter)
                                        @php
                                            $charterStatusClasses = [
                                                'draft' => 'text-gray-600 font-semibold bg-gray-100/80 px-2 py-0.5 rounded-lg border border-gray-200/60',
                                                'submitted' => 'text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60',
                                            ][$project->charter->status] ?? 'text-gray-600';
                                        @endphp
                                        {{ __('Project Charter sudah dibuat. Status:') }} 
                                        <span class="inline-block {{ $charterStatusClasses }}">
                                            {{ $project->charter->status === 'submitted' ? __('Finalized') : ucfirst($project->charter->status) }}
                                        </span>
                                    @else
                                        {{ __('Project Charter belum dibuat untuk proyek ini.') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            @if(!$project->charter && strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                                <a href="{{ route('projects.charter.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-indigo-500/10 transition gap-2">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    {{ __('Buat Charter') }}
                                </a>
                            @elseif($project->charter)
                                <a href="{{ route('projects.charter.show', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-2">
                                    <i class="fas fa-eye text-gray-400"></i>
                                    {{ __('Lihat Charter') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Project Scope Hub Card (Only shown if status is planning) -->
                @if($project->status === 'planning')
                    @php
                        $showScopeCard = false;
                        if ($userRole === 'manager') {
                            $showScopeCard = true;
                        } elseif (in_array($userRole, ['pmo', 'project management officer'])) {
                            $showScopeCard = true;
                        } elseif ($userRole === 'project manager') {
                            $showScopeCard = ($project->owner_id === Auth::id());
                        }
                    @endphp

                    @if($showScopeCard)
                        <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50/80 via-teal-50/30 to-white border border-emerald-100/60 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-emerald-600/10 text-emerald-600 rounded-2xl border border-emerald-200/50">
                                    <i class="fas fa-sitemap text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-primaryText">{{ __('Project Scope') }}</h4>
                                    <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                                        @if($project->scope)
                                            @php
                                                $scopeStatusClasses = [
                                                    'draft' => 'text-gray-600 font-semibold bg-gray-100/80 px-2 py-0.5 rounded-lg border border-gray-200/60',
                                                    'finalized' => 'text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200/60',
                                                ][$project->scope->status] ?? 'text-gray-600';
                                            @endphp
                                            {{ __('Project Scope sudah dibuat. Status:') }} 
                                            <span class="inline-block {{ $scopeStatusClasses }}">
                                                {{ $project->scope->status === 'finalized' ? __('Finalized') : ucfirst($project->scope->status) }}
                                            </span>
                                        @else
                                            {{ __('Project Scope belum dibuat untuk proyek ini.') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-center">
                                @if(!$project->scope && $userRole === 'manager')
                                    <a href="{{ route('projects.scope.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-2">
                                        <i class="fas fa-plus text-[10px]"></i>
                                        {{ __('Buat Scope') }}
                                    </a>
                                @elseif($project->scope)
                                    <a href="{{ route('projects.scope.show', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-2">
                                        <i class="fas fa-eye text-gray-400"></i>
                                        {{ __('Lihat Scope') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Checklist Kelengkapan Dokumen Inisiasi (Only for Manager when project is approved) -->
                @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                    <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm space-y-3">
                        <h4 class="text-sm font-bold text-primaryText flex items-center gap-1.5">
                            <i class="fas fa-clipboard-list text-gray-500"></i>
                            {{ __('Checklist Kelengkapan Dokumen Inisiasi') }}
                        </h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-secondaryText flex items-center gap-2">
                                    <i class="fas fa-file-contract text-blue-500 w-4"></i> Project Proposal
                                </span>
                                @if($project->proposal && $project->proposal->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                                        <i class="fas fa-check-circle"></i> {{ __('Selesai (Finalized)') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                                        <i class="fas fa-exclamation-circle"></i> {{ __('Belum Lengkap / Draft') }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-secondaryText flex items-center gap-2">
                                    <i class="fas fa-file-signature text-indigo-500 w-4"></i> Project Charter
                                </span>
                                @if($project->charter && $project->charter->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                                        <i class="fas fa-check-circle"></i> {{ __('Selesai (Finalized)') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                                        <i class="fas fa-exclamation-circle"></i> {{ __('Belum Lengkap / Draft') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $proposalCompleted = $project->proposal && $project->proposal->status === 'submitted';
                            $charterCompleted = $project->charter && $project->charter->status === 'submitted';
                            $allCompleted = $proposalCompleted && $charterCompleted;
                        @endphp

                        <div class="pt-2 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            @if($allCompleted)
                                <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                    <i class="fas fa-check-double text-emerald-500"></i> {{ __('Dokumen inisiasi lengkap. Proyek siap dilanjutkan ke Planning.') }}
                                </span>
                                <a href="{{ route('projects.edit', $project->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition gap-1.5">
                                    <i class="fas fa-arrow-right text-[10px]"></i> {{ __('Lanjutkan ke Planning') }}
                                </a>
                            @else
                                <span class="text-amber-700 font-medium italic">
                                    {{ __('Lengkapi dan finalisasi kedua dokumen untuk melanjutkan ke tahap Planning.') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div>
                    <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Deskripsi Proyek') }}</h3>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                        {{ $project->description ?: __('Tidak ada deskripsi yang ditambahkan untuk proyek ini.') }}
                    </div>
                </div>

                <!-- Dates and Management Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-3">{{ __('Rentang Waktu') }}</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="far fa-calendar-alt text-gray-400 w-4"></i>
                                <span class="text-secondaryText">{{ __('Tanggal Mulai:') }}</span>
                                <span class="font-medium text-primaryText">
                                    {{ $project->start_date ? $project->start_date->format('d F Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="far fa-calendar-check text-gray-400 w-4"></i>
                                <span class="text-secondaryText">{{ __('Tanggal Selesai:') }}</span>
                                <span class="font-medium text-primaryText">
                                    {{ $project->end_date ? $project->end_date->format('d F Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-3">{{ __('Personel Terkait') }}</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="far fa-user text-gray-400 w-4"></i>
                                <span class="text-secondaryText">{{ __('Pemilik/Pembuat Proyek:') }}</span>
                                <span class="font-medium text-primaryText">
                                    {{ $project->owner ? $project->owner->name : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="far fa-user-circle text-gray-400 w-4"></i>
                                <span class="text-secondaryText">{{ __('Manager Pendamping:') }}</span>
                                <span class="font-medium text-primaryText">
                                    {{ $project->manager ? $project->manager->name : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Metadata Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 text-xs text-secondaryText">
                    <div>
                        <span>{{ __('Dibuat pada: ') }}</span>
                        <span class="font-medium text-primaryText">{{ $project->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        <span>{{ __('Pembaruan Terakhir: ') }}</span>
                        <span class="font-medium text-primaryText">{{ $project->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>

                <!-- Footer Actions Contextual -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                        {{ __('Kembali') }}
                    </a>

                    @if(strtolower(Auth::user()->role) === 'project manager' && $project->owner_id === Auth::id() && in_array($project->status, ['draft', 'rejected']))
                        <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition duration-200">
                            <i class="fas fa-edit mr-1"></i> {{ __('Ubah Detail / Ajukan') }}
                        </a>
                    @endif

                    @if(strtolower(Auth::user()->role) === 'manager' && in_array($project->status, ['submitted', 'approved']))
                        <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-amber-500/10 transition duration-200">
                            <i class="fas fa-cog mr-1"></i> {{ __('Tinjau & Ubah Status') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
