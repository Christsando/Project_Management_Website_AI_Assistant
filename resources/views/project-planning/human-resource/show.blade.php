<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="p-6 bg-white rounded-2xl border-slate-100 border shadow-sm">
        <div class="w-full mx-auto space-y-6">

            <!-- Top Sub-Navigation Tabs (Redesigned as sleek pill tabs) -->
            @include('project-planning.human-resource.partials.sub-navigation')

            <!-- Back Navigation & Header Section -->
            <div class="space-y-4">
                @include('project-planning.human-resource.partials.sub-header', [
                    'breadcrumb' => __('Perencanaan Proyek') . ' > ' . __('Perencanaan SDM'),
                    'title' => __('Alokasi & Kapasitas Tim'),
                    'description' => __('Kelola beban kerja personil dan alokasikan peran strategis untuk memastikan keberhasilan proyek tepat waktu.'),
                    'project' => $project,
                    'actionButtonEnabled' => $isPmo && $isDraft,
                ])
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            @endif

            <!-- Finalized / Draft Banner -->
            @include('project-planning.human-resource.partials.finalized-draf-banner')

            <!-- Plan content -->
            @if (!$hrPlan)
                @include('project-planning.human-resource.partials.draf-message')
            @else
                <!-- Metric Cards Row (Span full width) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @include('project-planning.human-resource.partials.total-personil-card')
                    @include('project-planning.human-resource.partials.avarage-weight-card')
                    @include('project-planning.human-resource.partials.project-quality-card')
                </div>

                <!-- Main Resource List Table Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    @include('project-planning.human-resource.partials.title-action',['isEditable' => false ])

                    <!-- Table -->
                    @if ($hrItems->isEmpty())
                        @include('project-planning.human-resource.partials.empty-message',['isEditable' => false ])
                    @else
                        <div class="overflow-x-auto">
                            @include('project-planning.human-resource.partials.workload-table')
                        </div>

                        <!-- Footer Pagination / Stats -->
                        <div
                            class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500 font-medium">
                            <div id="pagination-stats">
                                {{ __('Menampilkan ') }}<span class="font-bold text-slate-700"
                                    id="visible-count">{{ $hrItems->count() }}</span>{{ __(' dari ') }}<span
                                    class="font-bold text-slate-700">{{ $hrItems->count() }}</span>{{ __(' personil') }}
                            </div>
                            <div class="inline-flex gap-1">
                                <button type="button" disabled
                                    class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Sebelumnya</button>
                                <button type="button"
                                    class="px-3 py-1 border border-slate-800 bg-slate-800 text-white rounded-lg text-[11px] font-bold">1</button>
                                <button type="button" disabled
                                    class="px-3 py-1 border border-slate-200 rounded-lg text-slate-400 bg-slate-50 cursor-not-allowed text-[11px] font-bold">Selanjutnya</button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bottom Section (Two Columns) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Column 1: Skill Distribution -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                            <i class="fas fa-info-circle text-slate-400"></i>
                            <h4 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider">
                                {{ __('Distribusi Keahlian') }}</h4>
                        </div>
                        <div class="space-y-3.5 text-xs font-bold text-slate-750">
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Frontend Development</span>
                                    <span class="font-mono text-slate-800">40%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-700 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>Backend Development</span>
                                    <span class="font-mono text-slate-800">35%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-600 rounded-full" style="width: 35%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>UI/UX Design</span>
                                    <span class="font-mono text-slate-800">15%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-500 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <span>DevOps & Testing</span>
                                    <span class="font-mono text-slate-800">10%</span>
                                </div>
                                <div
                                    class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                                    <div class="h-full bg-slate-400 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Prediksi Ketersediaan Tim (CSS Bar Chart) -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <h4
                                class="font-extrabold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                {{ __('Prediksi Ketersediaan Tim') }}
                            </h4>
                            <i class="fas fa-chart-bar text-slate-450"></i>
                        </div>
                        <!-- Pure CSS vertical bars chart matching mockup -->
                        <div
                            class="flex items-end justify-between h-44 pt-4 px-2 font-mono text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-100 rounded-lg h-20 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>JAN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-150 rounded-lg h-24 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>FEB</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-[#0B1329] rounded-lg h-32 shadow-sm transition-all duration-300 hover:bg-slate-800">
                                </div>
                                <span class="text-slate-800 font-black">MAR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-200 rounded-lg h-28 transition-all duration-300 hover:bg-slate-350">
                                </div>
                                <span>APR</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-100 rounded-lg h-12 transition-all duration-300 hover:bg-slate-150">
                                </div>
                                <span>MEI</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-150 rounded-lg h-16 transition-all duration-300 hover:bg-slate-200">
                                </div>
                                <span>JUN</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div
                                    class="w-9 bg-slate-200 rounded-lg h-20 transition-all duration-300 hover:bg-slate-250">
                                </div>
                                <span>JUL</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Card (Displaying Notes elegantly at the bottom) -->
                @if ($hrPlan->notes)
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <h4
                            class="font-extrabold text-xs uppercase text-slate-400 tracking-wider flex items-center gap-2 mb-3">
                            <i class="fas fa-sticky-note text-[#0B1329]"></i>
                            {{ __('Catatan Perencanaan SDM') }}
                        </h4>
                        <p
                            class="text-xs text-slate-500 leading-relaxed font-semibold bg-slate-50 p-4 rounded-xl border border-slate-100/60 whitespace-pre-line">
                            {{ $hrPlan->notes }}
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
