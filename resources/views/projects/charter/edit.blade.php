<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.charter.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Tampilan Charter') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Ubah Project Charter') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-semibold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Messages -->
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Column (Left 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                        <form action="{{ route('projects.charter.update', $project->id) }}" method="POST" id="charterForm">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Project Purpose -->
                                <div>
                                    <label for="project_purpose" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tujuan Proyek (Project Purpose)') }}</label>
                                    <textarea name="project_purpose" id="project_purpose" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Mengapa proyek ini dilaksanakan? Apa tujuan strategisnya?">{{ old('project_purpose', $charter->project_purpose) }}</textarea>
                                    @error('project_purpose')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['project_purpose']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-project_purpose">{{ $suggestions['project_purpose'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('project_purpose')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_purpose').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Business Case -->
                                <div>
                                    <label for="business_case" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kasus Bisnis (Business Case)') }}</label>
                                    <textarea name="business_case" id="business_case" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Analisis manfaat finansial/operasional dan alasan investasi proyek ini...">{{ old('business_case', $charter->business_case) }}</textarea>
                                    @error('business_case')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['business_case']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-business_case">{{ $suggestions['business_case'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('business_case')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-business_case').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Objectives -->
                                <div>
                                    <label for="project_objectives" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Sasaran Proyek (Project Objectives)') }}</label>
                                    <textarea name="project_objectives" id="project_objectives" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Target spesifik yang terukur, dapat dicapai, relevan, dan berjangka waktu (SMART)...">{{ old('project_objectives', $charter->project_objectives) }}</textarea>
                                    @error('project_objectives')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['project_objectives']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-project_objectives">{{ $suggestions['project_objectives'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('project_objectives')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_objectives').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Scope Summary -->
                                <div>
                                    <label for="scope_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Ruang Lingkup (Scope Summary)') }}</label>
                                    <textarea name="scope_summary" id="scope_summary" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Ringkasan pekerjaan utama yang termasuk dan tidak termasuk dalam proyek...">{{ old('scope_summary', $charter->scope_summary) }}</textarea>
                                    @error('scope_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['scope_summary']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-scope_summary">{{ $suggestions['scope_summary'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('scope_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-scope_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Success Criteria -->
                                <div>
                                    <label for="success_criteria" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kriteria Keberhasilan (Success Criteria)') }}</label>
                                    <textarea name="success_criteria" id="success_criteria" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Indikator atau ukuran yang membuktikan bahwa proyek telah berhasil...">{{ old('success_criteria', $charter->success_criteria) }}</textarea>
                                    @error('success_criteria')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['success_criteria']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-success_criteria">{{ $suggestions['success_criteria'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('success_criteria')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-success_criteria').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Assumptions -->
                                <div>
                                    <label for="assumptions" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Asumsi (Assumptions)') }}</label>
                                    <textarea name="assumptions" id="assumptions" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Hal-hal yang dianggap benar untuk keperluan perencanaan proyek...">{{ old('assumptions', $charter->assumptions) }}</textarea>
                                    @error('assumptions')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['assumptions']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-assumptions">{{ $suggestions['assumptions'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('assumptions')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-assumptions').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Constraints -->
                                <div>
                                    <label for="constraints" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Batasan (Constraints)') }}</label>
                                    <textarea name="constraints" id="constraints" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Keterbatasan seperti waktu, anggaran, teknologi, hukum, atau sumber daya...">{{ old('constraints', $charter->constraints) }}</textarea>
                                    @error('constraints')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['constraints']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-constraints">{{ $suggestions['constraints'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('constraints')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-constraints').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Stakeholder Summary -->
                                <div>
                                    <label for="stakeholder_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Pemangku Kepentingan (Stakeholder Summary)') }}</label>
                                    <textarea name="stakeholder_summary" id="stakeholder_summary" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Daftar pihak utama yang berkepentingan dan pengaruhnya terhadap proyek...">{{ old('stakeholder_summary', $charter->stakeholder_summary) }}</textarea>
                                    @error('stakeholder_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['stakeholder_summary']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-stakeholder_summary">{{ $suggestions['stakeholder_summary'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('stakeholder_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-stakeholder_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Milestone Summary -->
                                <div>
                                    <label for="milestone_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Milestone (Milestone Summary)') }}</label>
                                    <textarea name="milestone_summary" id="milestone_summary" rows="3" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Tuliskan tahapan-tahapan penting proyek beserta target tanggal penyelesaiannya...">{{ old('milestone_summary', $charter->milestone_summary) }}</textarea>
                                    @error('milestone_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['milestone_summary']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-milestone_summary">{{ $suggestions['milestone_summary'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('milestone_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-milestone_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Budget Summary -->
                                <div>
                                    <label for="budget_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Anggaran (Budget Summary)') }}</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="budget_summary" id="budget_summary" step="0.01" min="0" value="{{ old('budget_summary', $charter->budget_summary) }}"
                                               class="w-full pl-9 pr-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                               placeholder="0.00">
                                    </div>
                                    @error('budget_summary')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['budget_summary']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-budget_summary">{{ $suggestions['budget_summary'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('budget_summary')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-budget_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100">
                                <div>
                                    <button type="submit" id="btn-generate-ai" name="action" value="generate_ai" 
                                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 hover:text-indigo-800 rounded-xl text-sm font-semibold shadow-sm transition duration-200 gap-1.5">
                                        <i class="fas fa-magic text-indigo-500"></i>
                                        {{ __('Perbarui Rekomendasi AI') }}
                                    </button>
                                </div>

                                <div class="flex items-center justify-end gap-3 w-full sm:w-auto">
                                    <a href="{{ route('projects.charter.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                        {{ __('Batal') }}
                                    </a>
                                    <button type="submit" name="action" value="save" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                        {{ __('Simpan Draf') }}
                                    </button>
                                    <button type="submit" name="action" value="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition duration-200">
                                        {{ __('Finalisasi Charter') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- AI Sidebar Column (Right 1/3) -->
                <div class="space-y-6">
                    <div class="bg-indigo-900/5 border border-indigo-100 p-6 rounded-xl shadow-sm relative overflow-hidden bg-white">
                        <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                            <i class="fas fa-robot text-sm"></i>
                        </div>
                        <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                            <i class="fas fa-magic"></i> {{ __('Rekomendasi AI Assistant') }}
                        </h3>

                        @if($charter->ai_suggestions)
                            <div class="bg-indigo-50/30 p-4 rounded-xl border border-indigo-100 text-xs text-primaryText leading-relaxed space-y-4">
                                <div class="max-h-[600px] overflow-y-auto font-sans text-indigo-950">
                                    @if($isJsonSuggestions)
                                        <div class="space-y-3">
                                            @foreach($suggestions as $key => $val)
                                                <div class="border-b border-indigo-100/40 pb-2">
                                                    <span class="font-bold text-[10px] uppercase text-indigo-600 block mb-0.5">
                                                        {{ str_replace('_', ' ', $key) }}
                                                    </span>
                                                    <p class="whitespace-pre-wrap">{{ $val }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="markdown-content markdown-content-sm">
                                            {!! str($charter->ai_suggestions)->markdown() !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button onclick="navigator.clipboard.writeText(document.getElementById('aiSuggestionsText').innerText); alert('Salin berhasil!');" 
                                        class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                    <i class="fas fa-copy mr-1"></i> {{ __('Salin Semua Rekomendasi') }}
                                </button>
                                <div id="aiSuggestionsText" class="hidden">
                                    @if($isJsonSuggestions)
                                        @foreach($suggestions as $key => $val)
                                            [{{ strtoupper(str_replace('_', ' ', $key)) }}]
                                            {{ $val }}

                                        @endforeach
                                    @else
                                        {{ $charter->ai_suggestions }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-indigo-100 bg-indigo-50/20 p-5 rounded-xl text-center">
                                <p class="text-sm font-semibold text-indigo-950 mb-1">{{ __('Belum Ada Rekomendasi AI') }}</p>
                                <p class="text-xs text-indigo-600/70 leading-relaxed mb-4">
                                    {{ __('Tekan tombol "Perbarui Rekomendasi AI" untuk menghasilkan analisis dan draf rekomendasi pengisian Project Charter secara otomatis.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Manager Feedback Sidebar Card -->
                    @if($charter->feedback_notes)
                        <div class="bg-white p-5 rounded-xl border border-amber-200 shadow-sm">
                            <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fas fa-comment-dots"></i> {{ __('Catatan Revisi Manager') }}
                            </h3>
                            <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100 text-xs text-amber-900 whitespace-pre-line leading-relaxed">
                                {{ $charter->feedback_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

        function useAiSuggestion(fieldId) {
            const textElement = document.getElementById('ai-suggest-' + fieldId);
            const textarea = document.getElementById(fieldId);
            if (textElement && textarea) {
                let val = textElement.innerText.trim();
                if (fieldId === 'budget_summary' || fieldId === 'estimated_budget') {
                    // Clean text to get clean numbers for budget
                    const match = val.replace(/[^0-9]/g, '');
                    if (match) {
                        val = match;
                    }
                }
                textarea.value = val;
                textarea.dispatchEvent(new Event('input'));
            }
        }
    </script>
</x-app-layout>
