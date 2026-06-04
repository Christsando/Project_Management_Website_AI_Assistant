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
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                    <div class="h-full bg-slate-700 rounded-full" style="width: 40%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1.5">
                    <span>Backend Development</span>
                    <span class="font-mono text-slate-800">35%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                    <div class="h-full bg-slate-600 rounded-full" style="width: 35%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1.5">
                    <span>UI/UX Design</span>
                    <span class="font-mono text-slate-800">15%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                    <div class="h-full bg-slate-500 rounded-full" style="width: 15%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1.5">
                    <span>DevOps & Testing</span>
                    <span class="font-mono text-slate-800">10%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200/50">
                    <div class="h-full bg-slate-400 rounded-full" style="width: 10%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 2: Prediksi Ketersediaan Tim (CSS Bar Chart) -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
            <h4 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-2">
                {{ __('Prediksi Ketersediaan Tim') }}
            </h4>
            <i class="fas fa-chart-bar text-slate-450"></i>
        </div>
        <!-- Pure CSS vertical bars chart matching mockup -->
        <div
            class="flex items-end justify-between h-44 pt-4 px-2 font-mono text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-100 rounded-lg h-20 transition-all duration-300 hover:bg-slate-200">
                </div>
                <span>JAN</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-150 rounded-lg h-24 transition-all duration-300 hover:bg-slate-200">
                </div>
                <span>FEB</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-[#0B1329] rounded-lg h-32 shadow-sm transition-all duration-300 hover:bg-slate-800">
                </div>
                <span class="text-slate-800 font-black">MAR</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-200 rounded-lg h-28 transition-all duration-300 hover:bg-slate-350">
                </div>
                <span>APR</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-100 rounded-lg h-12 transition-all duration-300 hover:bg-slate-150">
                </div>
                <span>MEI</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-150 rounded-lg h-16 transition-all duration-300 hover:bg-slate-200">
                </div>
                <span>JUN</span>
            </div>
            <div class="flex flex-col items-center gap-2 flex-1">
                <div class="w-9 bg-slate-200 rounded-lg h-20 transition-all duration-300 hover:bg-slate-250">
                </div>
                <span>JUL</span>
            </div>
        </div>
    </div>
</div>
