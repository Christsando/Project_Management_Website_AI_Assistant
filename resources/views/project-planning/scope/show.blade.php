<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.scope.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar Scope') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Detail Project Scope') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                    </h3>
                </div>

                @php
                    $userRole = strtolower(Auth::user()->role);
                    $isManager = ($userRole === 'manager');
                    $isDraft = ($scope && $scope->status === 'draft');
                    $isFinalized = ($scope && $scope->status === 'finalized');
                @endphp

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-gray-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>

                    @if($isDraft && $isManager)
                        <a href="{{ route('projects.scope.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 border border-amber-300 text-amber-700 hover:bg-amber-600 hover:text-white rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                            <i class="fas fa-edit"></i>
                            {{ __('Ubah Scope') }}
                        </a>

                        <form action="{{ route('projects.scope.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi Project Scope ini? Setelah finalized, data tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Scope') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- WBS Readiness Indicator Banner -->
            @if($isFinalized)
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('Project Scope Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-check-double text-blue-600 mr-1"></i>
                            {{ __('Siap digunakan untuk WBS (Work Breakdown Structure).') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf Project Scope') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            {{ __('Status dokumen masih berupa draf. Finalisasikan dokumen untuk mengunci data dan melanjutkan ke perencanaan WBS.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Scope Details -->
            <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6 space-y-6">
                <!-- Objective & Scope Description -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Tujuan Proyek (Objective)') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->objective }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Deskripsi Ruang Lingkup') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->scope_description }}
                        </div>
                    </div>
                </div>

                <!-- In-Scope vs Out-of-Scope -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xs font-semibold text-emerald-800 uppercase tracking-wider mb-2">{{ __('Pekerjaan yang Termasuk (In-Scope)') }}</h3>
                        <div class="bg-emerald-50/20 p-4 rounded-xl border border-emerald-100/60 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->in_scope }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-rose-800 uppercase tracking-wider mb-2">{{ __('Pekerjaan yang Tidak Termasuk (Out-of-Scope)') }}</h3>
                        <div class="bg-rose-50/20 p-4 rounded-xl border border-rose-100/60 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->out_of_scope }}
                        </div>
                    </div>
                </div>

                <!-- Deliverables & Acceptance Criteria -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Hasil Kerja (Deliverables)') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->deliverables }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Kriteria Penerimaan (Acceptance Criteria)') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->acceptance_criteria }}
                        </div>
                    </div>
                </div>

                <!-- Requirements, Assumptions, Constraints, Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Persyaratan Utama') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->main_requirements ?: __('Tidak ada persyaratan utama tambahan.') }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Asumsi') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->assumptions ?: __('Tidak ada asumsi khusus.') }}
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Batasan / Kendala') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->constraints ?: __('Tidak ada batasan khusus.') }}
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <h3 class="text-xs font-semibold text-secondaryText uppercase tracking-wider mb-2">{{ __('Catatan Tambahan') }}</h3>
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                            {{ $scope->notes ?: __('Tidak ada catatan tambahan.') }}
                        </div>
                    </div>
                </div>

                <!-- Metadata Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-gray-100 text-xs text-secondaryText">
                    <div>
                        <span>{{ __('Dibuat Oleh: ') }}</span>
                        <span class="font-bold text-primaryText">{{ $scope->creator ? $scope->creator->name : '-' }}</span>
                    </div>
                    <div>
                        <span>{{ __('Pembaruan Terakhir Oleh: ') }}</span>
                        <span class="font-bold text-primaryText">{{ $scope->updater ? $scope->updater->name : '-' }}</span>
                    </div>
                    <div>
                        <span>{{ __('Pembaruan Terakhir: ') }}</span>
                        <span class="font-bold text-primaryText">{{ $scope->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
