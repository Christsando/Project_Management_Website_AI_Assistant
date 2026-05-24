<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.proposal.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Tampilan Proposal') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Ubah Proposal Proyek') }}
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
                if ($proposal->ai_suggestions) {
                    $decoded = json_decode($proposal->ai_suggestions, true);
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
                        <form action="{{ route('projects.proposal.update', $project->id) }}" method="POST" id="proposalForm">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Background -->
                                <div>
                                    <label for="background" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Latar Belakang') }}</label>
                                    <textarea name="background" id="background" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Jelaskan masalah atau peluang yang melatarbelakangi proyek ini...">{{ old('background', $proposal->background) }}</textarea>
                                    @error('background')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['background']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-background">{{ $suggestions['background'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('background')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-background').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Objectives -->
                                <div>
                                    <label for="objectives" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tujuan Proyek (Objectives)') }}</label>
                                    <textarea name="objectives" id="objectives" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Apa yang ingin dicapai melalui proyek ini? Sebutkan target spesifiknya...">{{ old('objectives', $proposal->objectives) }}</textarea>
                                    @error('objectives')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['objectives']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-objectives">{{ $suggestions['objectives'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('objectives')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-objectives').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Initial Needs -->
                                <div>
                                    <label for="initial_needs" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kebutuhan Awal') }}</label>
                                    <textarea name="initial_needs" id="initial_needs" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Sebutkan sumber daya, teknologi, atau dukungan awal yang dibutuhkan...">{{ old('initial_needs', $proposal->initial_needs) }}</textarea>
                                    @error('initial_needs')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['initial_needs']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-initial_needs">{{ $suggestions['initial_needs'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('initial_needs')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-initial_needs').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Overview -->
                                <div>
                                    <label for="project_overview" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Gambaran Umum Proyek') }}</label>
                                    <textarea name="project_overview" id="project_overview" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Penjelasan ringkas bagaimana proyek ini akan berjalan...">{{ old('project_overview', $proposal->project_overview) }}</textarea>
                                    @error('project_overview')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['project_overview']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-project_overview">{{ $suggestions['project_overview'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('project_overview')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_overview').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Scope Overview -->
                                <div>
                                    <label for="scope_overview" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Gambaran Ruang Lingkup (Scope)') }}</label>
                                    <textarea name="scope_overview" id="scope_overview" rows="4" 
                                              class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                              placeholder="Batasan proyek, apa saja yang masuk dan tidak masuk dalam ruang lingkup proyek...">{{ old('scope_overview', $proposal->scope_overview) }}</textarea>
                                    @error('scope_overview')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['scope_overview']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-scope_overview">{{ $suggestions['scope_overview'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('scope_overview')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-scope_overview').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-copy"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Estimated Budget -->
                                <div>
                                    <label for="estimated_budget" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Perkiraan Anggaran (Estimated Budget)') }}</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="estimated_budget" id="estimated_budget" step="0.01" min="0" value="{{ old('estimated_budget', $proposal->estimated_budget) }}"
                                               class="w-full pl-9 pr-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                               placeholder="0.00">
                                    </div>
                                    @error('estimated_budget')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    @if(isset($suggestions['estimated_budget']))
                                        <div class="mt-2 p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/50 text-xs">
                                            <span class="font-bold text-indigo-950 block mb-1"><i class="fas fa-robot text-indigo-500 mr-1"></i> Rekomendasi AI:</span>
                                            <p class="text-indigo-900 leading-relaxed mb-2" id="ai-suggest-estimated_budget">{{ $suggestions['estimated_budget'] }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="useAiSuggestion('estimated_budget')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-semibold transition">
                                                    <i class="fas fa-arrow-up"></i> Gunakan Saran
                                                </button>
                                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-estimated_budget').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-semibold transition">
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
                                    <a href="{{ route('projects.proposal.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                        {{ __('Batal') }}
                                    </a>
                                    <button type="submit" name="action" value="save" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition duration-200">
                                        {{ __('Simpan Draf') }}
                                    </button>
                                    <button type="submit" name="action" value="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition duration-200">
                                        {{ __('Finalisasi Proposal') }}
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

                        @if($proposal->ai_suggestions)
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
                                            {!! str($proposal->ai_suggestions)->markdown() !!}
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
                                        {{ $proposal->ai_suggestions }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-indigo-100 bg-indigo-50/20 p-5 rounded-xl text-center">
                                <p class="text-sm font-semibold text-indigo-950 mb-1">{{ __('Belum Ada Rekomendasi AI') }}</p>
                                <p class="text-xs text-indigo-600/70 leading-relaxed mb-4">
                                    {{ __('Tekan tombol "Perbarui Rekomendasi AI" untuk menghasilkan analisis dan draf rekomendasi pengisian Project Proposal secara otomatis.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
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

        function useAiSuggestion(fieldId) {
            const textElement = document.getElementById('ai-suggest-' + fieldId);
            const textarea = document.getElementById(fieldId);
            if (textElement && textarea) {
                textarea.value = textElement.innerText.trim();
                textarea.dispatchEvent(new Event('input'));
            }
        }
    </script>
</x-app-layout>
