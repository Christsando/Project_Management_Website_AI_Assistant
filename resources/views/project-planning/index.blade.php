<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-2 pb-12">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Title section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Project Planning') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Rencanakan ruang lingkup, struktur kerja (WBS), jadwal, alokasi sumber daya, anggaran, dan mitigasi risiko proyek.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm hover:shadow transition gap-1.5">
                        <i class="fas fa-list-ul"></i>
                        {{ __('Lihat Daftar Project Planning') }}
                    </a>
                </div>
            </div>

            <!-- Content Area Placeholder -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                <!-- Scope & WBS Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-blue-500/50 hover:shadow-md transition duration-200">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-sitemap text-sm"></i>
                            </div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ __('Scope & WBS') }}</h4>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-4">{{ __('Kelola ruang lingkup proyek dan susun Work Breakdown Structure (WBS) secara hierarkis.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4 border-t border-slate-50 pt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Aktif') }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('project-planning.scope.index') }}" class="inline-flex items-center justify-center px-3.5 py-2 bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200/80 hover:border-blue-200 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200 gap-1.5">
                                <i class="fas fa-compass text-slate-400 group-hover:text-blue-500 text-[10px]"></i>
                                {{ __('Scope') }}
                            </a>
                            <a href="{{ route('project-planning.wbs.index') }}" class="inline-flex items-center justify-center px-3.5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm hover:shadow-md hover:shadow-blue-500/10 transition-all duration-200 gap-1.5">
                                <i class="fas fa-sitemap text-[10px]"></i>
                                {{ __('WBS') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline & Gantt Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-blue-500/50 hover:shadow-md transition duration-200">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-purple-50 text-purple-600 border border-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-stream text-sm"></i>
                            </div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ __('Timeline & Gantt Chart') }}</h4>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-4">{{ __('Jadwalkan milestone penting proyek dan visualisasikan kemajuan menggunakan Gantt Chart interaktif.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4 border-t border-slate-50 pt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Aktif') }}
                        </span>
                        <a href="{{ route('project-planning.timeline.index') }}" class="group inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm hover:shadow-md hover:shadow-blue-500/10 transition-all duration-200 gap-1.5">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                            {{ __('Timeline') }}
                            <i class="fas fa-chevron-right text-[9px] ml-0.5 transform group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Resource & Budget Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-blue-500/50 hover:shadow-md transition duration-200">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-wallet text-sm"></i>
                            </div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ __('Resource & Budget') }}</h4>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-4">{{ __('Alokasikan tim pelaksana (HR), atur penanggung jawab (PIC), dan susun rencana anggaran belanja proyek.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4 border-t border-slate-50 pt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Aktif') }}
                        </span>
                        <div class="flex items-center gap-2">
                            @if(in_array(strtolower(Auth::user()->role), ['manager', 'pmo', 'project management officer']))
                                <a href="{{ route('project-planning.human-resource.index') }}" class="inline-flex items-center justify-center px-3.5 py-2 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200/80 hover:border-emerald-200 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200 gap-1.5">
                                    <i class="fas fa-users-cog text-slate-400 group-hover:text-emerald-500 text-[10px]"></i>
                                    {{ __('HR') }}
                                </a>
                                <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center justify-center px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-semibold shadow-sm hover:shadow-md hover:shadow-emerald-500/10 transition-all duration-200 gap-1.5">
                                    <i class="fas fa-wallet text-[10px]"></i>
                                    {{ __('Budget') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Risk Management Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-blue-500/50 hover:shadow-md transition duration-200">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-rose-50 text-rose-500 border border-rose-100 rounded-xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-exclamation-triangle text-sm"></i>
                            </div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ __('Risk Management') }}</h4>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium mb-4">{{ __('Identifikasi daftar potensi risiko proyek, tentukan kategori dampak, dan susun matriks rencana mitigasi.') }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4 border-t border-slate-50 pt-4">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Aktif') }}
                        </span>
                        @if(in_array(strtolower(Auth::user()->role), ['manager', 'pmo', 'project management officer']))
                            <a href="{{ route('project-planning.risk-management.index') }}" class="group inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white rounded-xl text-xs font-semibold shadow-sm hover:shadow-md hover:shadow-rose-500/10 transition-all duration-200 gap-1.5">
                                <i class="fas fa-shield-alt text-[10px]"></i>
                                {{ __('Buka') }}
                                <i class="fas fa-chevron-right text-[9px] ml-0.5 transform group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
