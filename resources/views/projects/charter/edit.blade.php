<x-app-layout>
    <x-slot name="header">
        <x-header-component :title="'Project Charter: ' . $project->title" icon="fa-solid fa-file-signature text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <!-- Back Link & Top Info -->
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('projects.charter.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition gap-1.5">
                <i class="fas fa-arrow-left"></i>
                {{ __('Kembali ke Tampilan Charter') }}
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
            if ($charter->ai_suggestions) {
                $decoded = json_decode($charter->ai_suggestions, true);
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
                <form action="{{ route('projects.charter.update', $project->id) }}" method="POST" id="charterForm">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Card 1: Piagam Proyek Header -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'reviewed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'revision_needed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ][$charter->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses }}">
                                        {{ __('Status: ') . $charter->status }}
                                    </span>
                                </div>
                                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mb-1">
                                    {{ __('Piagam Proyek (Project Charter)') }}
                                </h2>
                                <p class="text-xs text-slate-500">
                                    {{ __('Lengkapi informasi dasar untuk memulai otorisasi proyek.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Card 2: Ringkasan Eksekutif -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fa-regular fa-file-lines text-sm"></i> {{ __('Ringkasan Eksekutif') }}
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Tujuan Proyek -->
                                <div>
                                    <label for="project_purpose" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Tujuan Proyek') }}</label>
                                    <textarea name="project_purpose" id="project_purpose" rows="3" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Apa hasil akhir yang ingin dicapai?">{{ old('project_purpose', $charter->project_purpose) }}</textarea>
                                    @error('project_purpose')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Business Case -->
                                <div>
                                    <label for="business_case" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Business Case') }}</label>
                                    <textarea name="business_case" id="business_case" rows="3" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Alasan strategis mengapa proyek ini dijalankan?">{{ old('business_case', $charter->business_case) }}</textarea>
                                    @error('business_case')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Objektif & Kriteria Sukses -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-sm"></i> {{ __('Objektif & Kriteria Sukses') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Objektif Utama -->
                                <div>
                                    <label for="project_objectives" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Objektif Utama') }}</label>
                                    <textarea name="project_objectives" id="project_objectives" rows="4" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Contoh: Digitalisasi HR">{{ old('project_objectives', $charter->project_objectives) }}</textarea>
                                    @error('project_objectives')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Kriteria Sukses -->
                                <div>
                                    <label for="success_criteria" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Kriteria Sukses') }}</label>
                                    <textarea name="success_criteria" id="success_criteria" rows="4" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Contoh: Efisiensi waktu 30%">{{ old('success_criteria', $charter->success_criteria) }}</textarea>
                                    @error('success_criteria')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Row Grid: Asumsi & Batasan + Stakeholders -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Card 4: Asumsi & Batasan -->
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                                <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-sm"></i> {{ __('Asumsi & Batasan') }}
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="assumptions" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Asumsi') }}</label>
                                        <textarea name="assumptions" id="assumptions" rows="3" 
                                                  class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                                  placeholder="Asumsi (e.g. Lisensi tersedia)">{{ old('assumptions', $charter->assumptions) }}</textarea>
                                        @error('assumptions')
                                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="constraints" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Batasan') }}</label>
                                        <textarea name="constraints" id="constraints" rows="3" 
                                                  class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                                  placeholder="Batasan (e.g. Budget maks 500jt)">{{ old('constraints', $charter->constraints) }}</textarea>
                                        @error('constraints')
                                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Card 5: Stakeholder Utama -->
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                                <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                    <i class="fas fa-users text-sm"></i> {{ __('Stakeholder Utama') }}
                                </h3>

                                <div>
                                    <label for="stakeholder_summary" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Ringkasan Pemangku Kepentingan') }}</label>
                                    <textarea name="stakeholder_summary" id="stakeholder_summary" rows="8" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Daftar stakeholder kunci (misal: John Doe - Project Sponsor)">{{ old('stakeholder_summary', $charter->stakeholder_summary) }}</textarea>
                                    @error('stakeholder_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Card 6: Ringkasan Ruang Lingkup -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <label for="scope_summary" class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">
                                {{ __('Ringkasan Ruang Lingkup (Scope Summary)') }}
                            </label>
                            <div class="relative">
                                <textarea name="scope_summary" id="scope_summary" rows="3" 
                                          class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                          placeholder="Ringkasan pekerjaan utama yang termasuk dan tidak termasuk dalam proyek...">{{ old('scope_summary', $charter->scope_summary) }}</textarea>
                            </div>
                            @error('scope_summary')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Card 7: Milestone & Anggaran -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fas fa-flag text-sm"></i> {{ __('Milestone & Anggaran') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Milestone Utama -->
                                <div>
                                    <label for="milestone_summary" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Milestone Utama') }}</label>
                                    <textarea name="milestone_summary" id="milestone_summary" rows="3" 
                                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition placeholder-slate-400" 
                                              placeholder="Tuliskan tahapan-tahapan penting proyek beserta target tanggal penyelesaiannya...">{{ old('milestone_summary', $charter->milestone_summary) }}</textarea>
                                    @error('milestone_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Total Anggaran -->
                                <div>
                                    <label for="budget_summary" class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Total Anggaran (IDR)') }}</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-slate-400 text-sm font-semibold">Rp</span>
                                        </div>
                                        <input type="number" name="budget_summary" id="budget_summary" step="0.01" min="0" value="{{ old('budget_summary', $charter->budget_summary) }}"
                                               class="w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl text-sm text-slate-800 transition font-semibold" 
                                               placeholder="0.00">
                                    </div>
                                    @error('budget_summary')
                                        <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                    @enderror
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
                            <a href="{{ route('projects.charter.show', $project->id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl text-xs font-bold transition">
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
                                {{ __('Finalisasi Charter') }}
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
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-bold rounded uppercase tracking-wider">Smart Recommendation</span>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        {{ __('Dapatkan saran cerdas untuk meningkatkan kualitas Project Charter Anda secara otomatis.') }}
                    </p>

                    @if($charter->ai_suggestions)
                        <div class="space-y-4">
                            @if($isJsonSuggestions)
                                <!-- Purpose Suggestion -->
                                @if(isset($suggestions['project_purpose']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-blue-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-regular fa-lightbulb text-blue-600"></i> Saran Tujuan Proyek
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-project_purpose">{{ $suggestions['project_purpose'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('project_purpose')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_purpose').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Business Case Suggestion -->
                                @if(isset($suggestions['business_case']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-indigo-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-chart-line text-indigo-600"></i> Saran Business Case
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-business_case">{{ $suggestions['business_case'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('business_case')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-business_case').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Objectives Suggestion -->
                                @if(isset($suggestions['project_objectives']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-purple-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-bullseye text-purple-600"></i> Saran Sasaran Proyek
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-project_objectives">{{ $suggestions['project_objectives'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('project_objectives')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_objectives').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Scope Summary Suggestion -->
                                @if(isset($suggestions['scope_summary']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-sky-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-compress-arrows-alt text-sky-600"></i> Saran Ruang Lingkup
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-scope_summary">{{ $suggestions['scope_summary'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('scope_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-scope_summary').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Success Criteria Suggestion -->
                                @if(isset($suggestions['success_criteria']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-emerald-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-check-double text-emerald-600"></i> Saran Kriteria Sukses
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-success_criteria">{{ $suggestions['success_criteria'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('success_criteria')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-success_criteria').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Assumptions Suggestion -->
                                @if(isset($suggestions['assumptions']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-teal-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-question text-teal-600"></i> Saran Asumsi
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-assumptions">{{ $suggestions['assumptions'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('assumptions')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-assumptions').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Constraints Suggestion -->
                                @if(isset($suggestions['constraints']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-rose-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Saran Batasan
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-constraints">{{ $suggestions['constraints'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('constraints')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-constraints').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Stakeholder Suggestion -->
                                @if(isset($suggestions['stakeholder_summary']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-violet-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-users text-violet-600"></i> Saran Pemangku Kepentingan
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-stakeholder_summary">{{ $suggestions['stakeholder_summary'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('stakeholder_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-stakeholder_summary').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Milestone Suggestion -->
                                @if(isset($suggestions['milestone_summary']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-pink-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-solid fa-flag text-pink-600"></i> Saran Milestone
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-milestone_summary">{{ $suggestions['milestone_summary'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('milestone_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Saran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-milestone_summary').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Budget Suggestion -->
                                @if(isset($suggestions['budget_summary']))
                                    <div class="bg-white p-4 rounded-xl border-l-4 border-amber-500 border border-y-slate-100 border-r-slate-100 shadow-sm space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-regular fa-money-bill-1 text-amber-600"></i> Analisis Anggaran
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-budget_summary">{{ $suggestions['budget_summary'] }}</p>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button" onclick="useAiSuggestion('budget_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-semibold shadow-sm transition">
                                                <i class="fas fa-check"></i> Gunakan Anggaran
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-budget_summary').innerText); alert('Salin berhasil!');" class="p-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg transition">
                                                <i class="fa-regular fa-copy text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="bg-indigo-50/20 p-4 rounded-xl border border-indigo-100 text-xs text-primaryText leading-relaxed">
                                    <div class="max-h-[400px] overflow-y-auto font-sans text-indigo-950 markdown-content markdown-content-sm" id="aiSuggestionsTextRaw">
                                        {!! str($charter->ai_suggestions)->markdown() !!}
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
                                {{ __('Tekan tombol "Perbarui Rekomendasi AI" untuk menghasilkan analisis draf charter.') }}
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
            const form = document.getElementById('charterForm');
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
                let val = textElement.innerText.trim();
                if (target.type === 'number') {
                    // Clean text to get clean numbers for budget
                    const match = val.replace(/rp/gi, '').replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.]/g, '');
                    let matched = match.match(/\d+(\.\d+)?/);
                    if (matched) {
                        val = matched[0];
                    } else {
                        alert("Tidak dapat mengekstrak angka otomatis dari saran. Silakan masukkan secara manual.");
                        return;
                    }
                }
                target.value = val;
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
