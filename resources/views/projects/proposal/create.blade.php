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
                    {{ __('Buat Proposal Proyek') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-semibold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                <form action="{{ route('projects.proposal.store', $project->id) }}" method="POST" id="proposalForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Background -->
                        <div>
                            <label for="background" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Latar Belakang') }}</label>
                            <textarea name="background" id="background" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Jelaskan masalah atau peluang yang melatarbelakangi proyek ini...">{{ old('background') }}</textarea>
                            @error('background')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Objectives -->
                        <div>
                            <label for="objectives" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tujuan Proyek (Objectives)') }}</label>
                            <textarea name="objectives" id="objectives" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Apa yang ingin dicapai melalui proyek ini? Sebutkan target spesifiknya...">{{ old('objectives') }}</textarea>
                            @error('objectives')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Initial Needs -->
                        <div>
                            <label for="initial_needs" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kebutuhan Awal') }}</label>
                            <textarea name="initial_needs" id="initial_needs" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Sebutkan sumber daya, teknologi, atau dukungan awal yang dibutuhkan...">{{ old('initial_needs') }}</textarea>
                            @error('initial_needs')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Overview -->
                        <div>
                            <label for="project_overview" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Gambaran Umum Proyek') }}</label>
                            <textarea name="project_overview" id="project_overview" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Penjelasan ringkas bagaimana proyek ini akan berjalan...">{{ old('project_overview') }}</textarea>
                            @error('project_overview')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scope Overview -->
                        <div class="md:col-span-2">
                            <label for="scope_overview" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Gambaran Ruang Lingkup (Scope)') }}</label>
                            <textarea name="scope_overview" id="scope_overview" rows="3" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Batasan proyek, apa saja yang masuk dan tidak masuk dalam ruang lingkup proyek...">{{ old('scope_overview') }}</textarea>
                            @error('scope_overview')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimated Budget -->
                        <div class="md:col-span-2">
                            <label for="estimated_budget" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Perkiraan Anggaran (Estimated Budget)') }}</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">Rp</span>
                                </div>
                                <input type="number" name="estimated_budget" id="estimated_budget" step="0.01" min="0" value="{{ old('estimated_budget') }}"
                                       class="w-full pl-9 pr-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                       placeholder="0.00">
                            </div>
                            @error('estimated_budget')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 border-t border-gray-100">
                        <!-- AI Suggestions Button -->
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
                                {{ __('Finalisasi Proposal') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
