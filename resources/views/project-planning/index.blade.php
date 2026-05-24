<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6">
            <!-- Title section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Project Planning') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Rencanakan ruang lingkup, struktur kerja (WBS), jadwal, alokasi sumber daya, anggaran, dan mitigasi risiko proyek.') }}
                    </h3>
                </div>
                <div>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                        <i class="fas fa-list-ul text-xs"></i>
                        {{ __('Lihat Daftar Project Planning') }}
                    </a>
                </div>
            </div>

            <!-- Content Area Placeholder -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                <!-- Scope & WBS Card -->
                <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm flex flex-col justify-between hover:border-primary/50 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                <i class="fas fa-sitemap text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText">{{ __('Scope & WBS') }}</h4>
                        </div>
                        <p class="text-sm text-secondaryText mb-4">{{ __('Kelola ruang lingkup proyek dan susun Work Breakdown Structure (WBS) secara hierarkis.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-50 text-green-800 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            {{ __('Aktif') }}
                        </span>
                        <div class="flex flex-col items-end gap-1">
                            <a href="{{ route('project-planning.scope.index') }}" class="text-xs text-primary hover:underline font-medium inline-flex items-center gap-1">
                                {{ __('Scope') }} <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                            <a href="{{ route('project-planning.wbs.index') }}" class="text-xs text-primary hover:underline font-medium inline-flex items-center gap-1">
                                {{ __('WBS') }} <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline & Gantt Card -->
                <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm flex flex-col justify-between hover:border-primary/50 transition">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                <i class="fas fa-stream text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText">{{ __('Timeline & Gantt Chart') }}</h4>
                        </div>
                        <p class="text-sm text-secondaryText mb-4">{{ __('Jadwalkan milestone penting proyek dan visualisasikan kemajuan menggunakan Gantt Chart interaktif.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-50 text-green-800 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            {{ __('Aktif') }}
                        </span>
                        <a href="{{ route('project-planning.timeline.index') }}" class="text-sm text-primary hover:underline font-medium inline-flex items-center gap-1">
                            {{ __('Buka') }} <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Resource & Budget Card -->
                <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                <i class="fas fa-users-cog text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText">{{ __('Resources & Budget') }}</h4>
                        </div>
                        <p class="text-sm text-secondaryText mb-4">{{ __('Alokasikan tim pelaksana (HR), atur penanggung jawab (PIC), dan susun rencana anggaran belanja proyek.') }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                            {{ __('Under Development') }}
                        </span>
                    </div>
                </div>

                <!-- Risk Management Card -->
                <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-lg text-primaryText">{{ __('Risk Management') }}</h4>
                        </div>
                        <p class="text-sm text-secondaryText mb-4">{{ __('Identifikasi daftar potensi risiko proyek, tentukan kategori dampak, dan susun matriks rencana mitigasi.') }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                            {{ __('Under Development') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
