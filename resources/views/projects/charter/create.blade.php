<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Detail Proyek') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Buat Project Charter') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-semibold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                <form action="{{ route('projects.charter.store', $project->id) }}" method="POST" id="charterForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Project Purpose -->
                        <div>
                            <label for="project_purpose" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tujuan Proyek (Project Purpose)') }}</label>
                            <textarea name="project_purpose" id="project_purpose" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Mengapa proyek ini dilaksanakan? Apa tujuan strategisnya?">{{ old('project_purpose') }}</textarea>
                            @error('project_purpose')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Case -->
                        <div>
                            <label for="business_case" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kasus Bisnis (Business Case)') }}</label>
                            <textarea name="business_case" id="business_case" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Analisis manfaat finansial/operasional dan alasan investasi proyek ini...">{{ old('business_case') }}</textarea>
                            @error('business_case')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Objectives -->
                        <div>
                            <label for="project_objectives" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Sasaran Proyek (Project Objectives)') }}</label>
                            <textarea name="project_objectives" id="project_objectives" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Target spesifik yang terukur, dapat dicapai, relevan, dan berjangka waktu (SMART)...">{{ old('project_objectives') }}</textarea>
                            @error('project_objectives')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scope Summary -->
                        <div>
                            <label for="scope_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Ruang Lingkup (Scope Summary)') }}</label>
                            <textarea name="scope_summary" id="scope_summary" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Ringkasan pekerjaan utama yang termasuk dan tidak termasuk dalam proyek...">{{ old('scope_summary') }}</textarea>
                            @error('scope_summary')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Success Criteria -->
                        <div>
                            <label for="success_criteria" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kriteria Keberhasilan (Success Criteria)') }}</label>
                            <textarea name="success_criteria" id="success_criteria" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Indikator atau ukuran yang membuktikan bahwa proyek telah berhasil...">{{ old('success_criteria') }}</textarea>
                            @error('success_criteria')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Assumptions -->
                        <div>
                            <label for="assumptions" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Asumsi (Assumptions)') }}</label>
                            <textarea name="assumptions" id="assumptions" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Hal-hal yang dianggap benar untuk keperluan perencanaan proyek...">{{ old('assumptions') }}</textarea>
                            @error('assumptions')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Constraints -->
                        <div>
                            <label for="constraints" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Batasan (Constraints)') }}</label>
                            <textarea name="constraints" id="constraints" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Keterbatasan seperti waktu, anggaran, teknologi, hukum, atau sumber daya...">{{ old('constraints') }}</textarea>
                            @error('constraints')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stakeholder Summary -->
                        <div>
                            <label for="stakeholder_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Pemangku Kepentingan (Stakeholder Summary)') }}</label>
                            <textarea name="stakeholder_summary" id="stakeholder_summary" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Daftar pihak utama yang berkepentingan dan pengaruhnya terhadap proyek...">{{ old('stakeholder_summary') }}</textarea>
                            @error('stakeholder_summary')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Milestone Summary -->
                        <div class="md:col-span-2">
                            <label for="milestone_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Milestone (Milestone Summary)') }}</label>
                            <textarea name="milestone_summary" id="milestone_summary" rows="3" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Tuliskan tahapan-tahapan penting proyek beserta target tanggal penyelesaiannya...">{{ old('milestone_summary') }}</textarea>
                            @error('milestone_summary')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Budget Summary -->
                        <div class="md:col-span-2">
                            <label for="budget_summary" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Ringkasan Anggaran (Budget Summary)') }}</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">Rp</span>
                                </div>
                                <input type="number" name="budget_summary" id="budget_summary" step="0.01" min="0" value="{{ old('budget_summary') }}"
                                       class="w-full pl-9 pr-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                       placeholder="0.00">
                            </div>
                            @error('budget_summary')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 border-t border-gray-100">
                        <!-- AI suggestions generate button -->
                        <div>
                            <button type="submit" id="btn-generate-ai" name="action" value="generate_ai" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 hover:text-indigo-800 rounded-xl text-sm font-semibold shadow-sm transition duration-200 gap-1.5">
                                <i class="fas fa-magic text-indigo-500"></i>
                                {{ __('Simpan & Buat Rekomendasi AI') }}
                            </button>
                        </div>

                        <div class="flex items-center justify-end gap-3 w-full sm:w-auto">
                            <a href="{{ route('projects.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" name="action" value="save" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                {{ __('Simpan Draf') }}
                            </button>
                            <button type="submit" name="action" value="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-indigo-500/10 transition duration-200">
                                {{ __('Finalisasi Charter') }}
                            </button>
                        </div>
                    </div>
                </form>
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
    </script>
</x-app-layout>
