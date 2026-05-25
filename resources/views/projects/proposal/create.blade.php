<x-app-layout>
    <x-slot name="header">
        <x-header-component title="Buat Proposal Proyek" icon="fa-regular fa-file-lines text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <form action="{{ route('projects.proposal.store', $project->id) }}" method="POST" id="proposalForm">
            @csrf

            <!-- Back Link & Top Info -->
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Detail Proyek') }}
                </a>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start mb-24">
                
                <!-- Left Column: Form Cards -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Card 1: Main Project Identity -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                        <div class="absolute top-6 right-6">
                            <button type="button" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-ellipsis-vertical text-lg"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                {{ __('Draf Baru') }}
                            </span>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mb-1">
                            {{ __('Identitas Proyek Utama') }}
                        </h2>
                        <p class="text-xs text-slate-500">
                            {{ __('Lengkapi detail fundamental untuk inisiasi proyek strategis Anda:') }} 
                            <span class="font-semibold text-slate-700">{{ $project->title }}</span>
                        </p>
                    </div>

                    <!-- Card 2: Latar Belakang -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <label for="background" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                            {{ __('Latar Belakang') }}
                        </label>
                        <div class="relative">
                            <textarea name="background" id="background" rows="4" 
                                      class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                      placeholder="Jelaskan alasan dan urgensi dibalik proyek ini...">{{ old('background') }}</textarea>
                        </div>
                        @error('background')
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card 3: Tujuan Proyek -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <label for="objectives" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                            {{ __('Tujuan Proyek (Objectives)') }}
                        </label>
                        <div class="relative">
                            <textarea name="objectives" id="objectives" rows="4" 
                                      class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                      placeholder="Apa yang ingin dicapai melalui proyek ini? Sebutkan target spesifiknya...">{{ old('objectives') }}</textarea>
                        </div>
                        @error('objectives')
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Row: Kebutuhan Awal & Gambaran Umum -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kebutuhan Awal -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <label for="initial_needs" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                                {{ __('Kebutuhan Awal') }}
                            </label>
                            <div class="relative">
                                <textarea name="initial_needs" id="initial_needs" rows="4" 
                                          class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                          placeholder="Sebutkan resource atau data yang diperlukan...">{{ old('initial_needs') }}</textarea>
                            </div>
                            @error('initial_needs')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gambaran Umum -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <label for="project_overview" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                                {{ __('Gambaran Umum Proyek') }}
                            </label>
                            <div class="relative">
                                <textarea name="project_overview" id="project_overview" rows="4" 
                                          class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                          placeholder="Ringkasan eksekusi proyek...">{{ old('project_overview') }}</textarea>
                            </div>
                            @error('project_overview')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Card 4: Gambaran Ruang Lingkup -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <label for="scope_overview" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                            {{ __('Gambaran Ruang Lingkup (Scope)') }}
                        </label>
                        <div class="relative">
                            <textarea name="scope_overview" id="scope_overview" rows="3" 
                                      class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                      placeholder="Batasan proyek, apa saja yang masuk dan tidak masuk dalam ruang lingkup proyek...">{{ old('scope_overview') }}</textarea>
                        </div>
                        @error('scope_overview')
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card 5: Perkiraan Anggaran -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <label for="estimated_budget" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                            {{ __('Perkiraan Anggaran (Estimated Budget)') }}
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-sm font-semibold">Rp</span>
                            </div>
                            <input type="number" name="estimated_budget" id="estimated_budget" step="0.01" min="0" value="{{ old('estimated_budget') }}"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition font-semibold" 
                                   placeholder="0.00">
                        </div>
                        @error('estimated_budget')
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Right Column: AI Sidebar -->
                <div class="space-y-6">
                    <!-- Asisten AI -->
                    <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm relative overflow-hidden">
                        <!-- Top decorative accent -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs shadow-sm">
                                    <i class="fas fa-magic"></i>
                                </span>
                                {{ __('Asisten AI') }}
                            </h3>
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-bold rounded uppercase tracking-wider">Beta</span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            {{ __('Dapatkan saran cerdas untuk meningkatkan kualitas proposal Anda secara otomatis.') }}
                        </p>

                        <!-- Empty State in Create View -->
                        <div class="border border-dashed border-slate-200 bg-slate-50/50 p-6 rounded-2xl text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-robot text-lg"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">{{ __('Belum Ada Rekomendasi AI') }}</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                {{ __('Tekan tombol "Simpan & Buat Rekomendasi AI" untuk menyimpan draf dan menganalisis proposal Anda.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Visual Project Chart Mockup -->
                    <div class="bg-slate-900 text-white p-5 rounded-2xl relative overflow-hidden shadow-md h-44 flex flex-col justify-between">
                        <!-- Background overlay graph -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none flex items-end">
                            <svg class="w-full h-24 text-blue-400" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0,80 Q25,40 50,65 T100,25 L100,100 L0,100 Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <span class="text-[9px] font-bold text-blue-400 uppercase tracking-wider block mb-1">ANALISIS VISUAL PROYEK</span>
                            <h4 class="text-sm font-extrabold tracking-tight">Proyeksi ROI 24 Bulan</h4>
                        </div>
                        <div class="relative z-10 flex justify-between items-end">
                            <span class="text-[10px] text-slate-400">Estimasi Efisiensi</span>
                            <span class="text-lg font-black text-blue-400">+34.5%</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="fixed bottom-0 left-0 right-0 md:left-60 bg-white/85 backdrop-blur-md border-t border-slate-100 p-4 flex flex-col sm:flex-row items-center justify-between gap-4 z-30 shadow-lg px-6 transition-all duration-300">
                <!-- Left: Collaborator status -->
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2 overflow-hidden">
                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-blue-500 text-white text-[9px] font-bold flex items-center justify-center">AA</div>
                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-emerald-500 text-white text-[9px] font-bold flex items-center justify-center">BS</div>
                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-amber-500 text-white text-[9px] font-bold flex items-center justify-center">CK</div>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500">3 {{ __('Kolaborator sedang mengedit') }}</span>
                </div>

                <!-- Right: Form actions -->
                <div class="flex flex-wrap items-center justify-end gap-2.5 w-full sm:w-auto">
                    <a href="{{ route('projects.show', $project->id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl text-xs font-bold transition">
                        {{ __('Batal') }}
                    </a>
                    
                    <button type="submit" name="action" value="save" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-800 rounded-xl text-xs font-bold transition">
                        {{ __('Simpan Draf') }}
                    </button>
                    
                    <button type="submit" id="btn-generate-ai" name="action" value="generate_ai" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 text-indigo-700 rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                        <i class="fas fa-magic text-indigo-500"></i>
                        {{ __('Simpan & Buat Rekomendasi AI') }}
                    </button>

                    <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-1.5">
                        {{ __('Finalisasi Proposal') }}
                        <i class="fa-regular fa-paper-plane text-[10px]"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- JS Form Submit Helper -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('proposalForm');
            const btnGenerate = document.getElementById('btn-generate-ai');
            
            if (form && btnGenerate) {
                btnGenerate.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Create a hidden input to preserve the action value
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'action';
                    hiddenInput.value = 'generate_ai';
                    form.appendChild(hiddenInput);

                    // Disable button and show loading state
                    btnGenerate.disabled = true;
                    btnGenerate.classList.add('opacity-75', 'cursor-not-allowed');
                    btnGenerate.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5 text-indigo-500"></i> {{ __("Sedang Memproses AI...") }}';
                    
                    // Disable other buttons to prevent concurrent action
                    const otherButtons = form.querySelectorAll('button[type="submit"]:not(#btn-generate-ai), a');
                    otherButtons.forEach(btn => {
                        if (btn.tagName === 'A') {
                            btn.classList.add('pointer-events-none', 'opacity-50');
                        } else {
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    });

                    // Submit the form
                    form.submit();
                });
            }
        });
    </script>
</x-app-layout>
