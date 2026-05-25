<x-app-layout>
    <x-slot name="header">
        <x-header-component title="Ubah Proposal Proyek" icon="fa-regular fa-file-lines text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <!-- Back Link & Top Info -->
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('projects.proposal.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition gap-1.5">
                <i class="fas fa-arrow-left"></i>
                {{ __('Kembali ke Tampilan Proposal') }}
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-500 text-sm"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @php
            $suggestions = [];
            $isJsonSuggestions = false;
            if ($proposal->ai_suggestions) {
                $decoded = json_decode($proposal->ai_suggestions, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $suggestions = $decoded;
                    $isJsonSuggestions = true;
                }
            }
        @endphp

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start mb-24">
            
            <!-- Left Column: Form Cards (2/3 Width) -->
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('projects.proposal.update', $project->id) }}" method="POST" id="proposalForm">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Card 1: Main Project Identity -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                            <div class="absolute top-6 right-6">
                                <button type="button" class="text-slate-400 hover:text-slate-600 transition">
                                    <i class="fas fa-ellipsis-vertical text-lg"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'reviewed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'revision_needed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ][$proposal->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses }}">
                                    {{ __('Status: ') . $proposal->status }}
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
                                          placeholder="Jelaskan alasan dan urgensi dibalik proyek ini...">{{ old('background', $proposal->background) }}</textarea>
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
                                          placeholder="Apa yang ingin dicapai melalui proyek ini? Sebutkan target spesifiknya...">{{ old('objectives', $proposal->objectives) }}</textarea>
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
                                              placeholder="Sebutkan resource atau data yang diperlukan...">{{ old('initial_needs', $proposal->initial_needs) }}</textarea>
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
                                              placeholder="Penjelasan ringkas eksekusi proyek...">{{ old('project_overview', $proposal->project_overview) }}</textarea>
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
                                          placeholder="Batasan proyek, apa saja yang masuk dan tidak masuk dalam ruang lingkup proyek...">{{ old('scope_overview', $proposal->scope_overview) }}</textarea>
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
                                <input type="number" name="estimated_budget" id="estimated_budget" step="0.01" min="0" value="{{ old('estimated_budget', $proposal->estimated_budget) }}"
                                       class="w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition font-semibold" 
                                       placeholder="0.00">
                            </div>
                            @error('estimated_budget')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
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
                            <a href="{{ route('projects.proposal.show', $project->id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl text-xs font-bold transition">
                                {{ __('Batal') }}
                            </a>
                            
                            <button type="submit" name="action" value="save" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-800 rounded-xl text-xs font-bold transition">
                                {{ __('Simpan Draf') }}
                            </button>
                            
                            <button type="submit" id="btn-generate-ai" name="action" value="generate_ai" 
                                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 text-indigo-700 rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                                <i class="fas fa-magic text-indigo-500"></i>
                                {{ __('Perbarui Rekomendasi AI') }}
                            </button>

                            <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-1.5">
                                {{ __('Finalisasi Proposal') }}
                                <i class="fa-regular fa-paper-plane text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Column: AI Sidebar (1/3 Width) -->
            <div class="space-y-6">
                <!-- Asisten AI Box -->
                <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm relative overflow-hidden">
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

                    @if($proposal->ai_suggestions)
                        <div class="space-y-4">
                            @if($isJsonSuggestions)
                                <!-- Background Suggestion -->
                                @if(isset($suggestions['background']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-blue-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-regular fa-lightbulb text-blue-600"></i> Saran Latar Belakang
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-background">{{ $suggestions['background'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('background')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-background').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Objectives Suggestion -->
                                @if(isset($suggestions['objectives']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-indigo-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-bullseye text-indigo-600"></i> Saran Tujuan Proyek
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-objectives">{{ $suggestions['objectives'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('objectives')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-objectives').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Initial Needs Suggestion -->
                                @if(isset($suggestions['initial_needs']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-purple-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-briefcase text-purple-600"></i> Saran Kebutuhan Awal
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-initial_needs">{{ $suggestions['initial_needs'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('initial_needs')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-initial_needs').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Project Overview Suggestion -->
                                @if(isset($suggestions['project_overview']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-sky-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-globe text-sky-600"></i> Saran Gambaran Umum
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-project_overview">{{ $suggestions['project_overview'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('project_overview')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_overview').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Scope Overview Suggestion -->
                                @if(isset($suggestions['scope_overview']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-amber-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-compress-arrows-alt text-amber-600"></i> Saran Ruang Lingkup
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-scope_overview">{{ $suggestions['scope_overview'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('scope_overview')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-scope_overview').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Estimated Budget Suggestion -->
                                @if(isset($suggestions['estimated_budget']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-emerald-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-regular fa-money-bill-1 text-emerald-600"></i> Optimalisasi Anggaran
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-estimated_budget">{{ $suggestions['estimated_budget'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('estimated_budget')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-estimated_budget').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="bg-indigo-50/20 p-4 rounded-xl border border-indigo-100 text-xs text-primaryText leading-relaxed">
                                    <div class="max-h-[400px] overflow-y-auto font-sans text-indigo-950 markdown-content markdown-content-sm" id="aiSuggestionsTextRaw">
                                        {!! str($proposal->ai_suggestions)->markdown() !!}
                                    </div>
                                    <button onclick="navigator.clipboard.writeText(document.getElementById('aiSuggestionsTextRaw').innerText); alert('Salin berhasil!');" 
                                            class="w-full mt-3 inline-flex items-center justify-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        <i class="fas fa-copy mr-1"></i> {{ __('Salin Semua Rekomendasi') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Empty State Suggestions -->
                        <div class="border border-dashed border-slate-200 bg-slate-50/50 p-6 rounded-2xl text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-robot text-lg"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">{{ __('Belum Ada Rekomendasi AI') }}</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                {{ __('Tekan tombol "Perbarui Rekomendasi AI" untuk menghasilkan analisis draf proposal.') }}
                            </p>
                        </div>
                    @endif
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
    </div>

    <!-- JS Helper Scripts -->
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
                    btnGenerate.innerHTML = '<i class="fas fa-magic mr-1.5 text-indigo-500 animate-pulse"></i> {{ __("Sedang Memproses AI...") }}';
                    
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

        function useAiSuggestion(fieldId) {
            const textElement = document.getElementById('ai-suggest-' + fieldId);
            const target = document.getElementById(fieldId);
            if (textElement && target) {
                let text = textElement.innerText.trim();
                if (target.type === 'number') {
                    let numericOnly = text.replace(/rp/gi, '').replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.]/g, '');
                    let matched = numericOnly.match(/\d+(\.\d+)?/);
                    if (matched) {
                        target.value = matched[0];
                    } else {
                        alert("Tidak dapat mengekstrak angka otomatis dari saran. Silakan masukkan secara manual.");
                        return;
                    }
                } else {
                    target.value = text;
                }
                target.dispatchEvent(new Event('input'));
                
                // Visual highlight effect
                target.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500');
                setTimeout(() => {
                    target.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500');
                }, 1500);
            }
        }
    </script>
</x-app-layout>
