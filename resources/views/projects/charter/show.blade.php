<x-app-layout>
    <x-slot name="header">
        <x-header-component :title="'Project Charter: ' . $project->title" icon="fa-solid fa-file-signature text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <!-- Back Link -->
        <div class="mb-4">
            <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition gap-1.5">
                <i class="fas fa-arrow-left"></i>
                {{ __('Kembali ke Detail Proyek') }}
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

        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                <span class="font-semibold">{{ session('info') }}</span>
            </div>
        @endif

        @if(!$charter)
            <!-- Empty State -->
            <div class="bg-white p-12 rounded-2xl border border-slate-100 shadow-sm text-center max-w-2xl mx-auto my-12">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 shadow-sm">
                    <i class="fa-solid fa-file-signature text-2xl"></i>
                </div>
                <h4 class="font-extrabold text-lg text-slate-800 mb-2">{{ __('Project Charter Belum Dibuat') }}</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                    {{ __('Project Charter mendefinisikan tujuan proyek, kasus bisnis, batasan, milestone, anggaran, dan pemangku kepentingan kunci.') }}
                </p>

                @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                    <a href="{{ route('projects.charter.create', $project->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                        <i class="fas fa-plus text-[10px]"></i>
                        {{ __('Buat Project Charter Sekarang') }}
                    </a>
                @else
                    <span class="inline-block text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-100 rounded-xl px-4 py-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Project Charter belum dibuat oleh Manager.') }}
                    </span>
                @endif
            </div>
        @else
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
                $userRole = strtolower(Auth::user()->role);
                $showAiSection = ($userRole === 'manager');
            @endphp

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start mb-24">
                
                <!-- Left Column: Details (2/3 Width) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Card 1: Header / Ringkasan Status -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'reviewed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'revision_needed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ][$charter->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses }}">
                                    {{ __('Status: ') . $charter->status }}
                                </span>
                            </div>
                            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mb-1">
                                {{ __('Piagam Proyek (Project Charter)') }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ __('Dokumen otorisasi resmi untuk pelaksanaan proyek.') }}
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
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Tujuan Proyek') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap">
                                    {{ $charter->project_purpose ?: __('Tidak ada detail tujuan proyek.') }}
                                </div>
                            </div>

                            <!-- Business Case -->
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Business Case') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap">
                                    {{ $charter->business_case ?: __('Tidak ada detail kasus bisnis.') }}
                                </div>
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
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Objektif Utama') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap min-h-[100px]">
                                    {{ $charter->project_objectives ?: __('Tidak ada detail sasaran proyek.') }}
                                </div>
                            </div>

                            <!-- Kriteria Sukses -->
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Kriteria Sukses') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap min-h-[100px]">
                                    {{ $charter->success_criteria ?: __('Tidak ada kriteria keberhasilan.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Ruang Lingkup & Milestone -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                            <i class="fa-solid fa-compress-arrows-alt text-sm"></i> {{ __('Ruang Lingkup & Milestone') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Scope Summary -->
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Ringkasan Ruang Lingkup') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap min-h-[100px]">
                                    {{ $charter->scope_summary ?: __('Tidak ada ringkasan ruang lingkup.') }}
                                </div>
                            </div>

                            <!-- Milestone Summary -->
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Ringkasan Milestone') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap min-h-[100px]">
                                    {{ $charter->milestone_summary ?: __('Tidak ada ringkasan milestone.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Asumsi, Batasan & Stakeholders -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Asumsi & Batasan -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-sm"></i> {{ __('Asumsi & Batasan') }}
                            </h3>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Asumsi') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap">
                                    {{ $charter->assumptions ?: __('Tidak ada detail asumsi.') }}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Batasan') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap">
                                    {{ $charter->constraints ?: __('Tidak ada detail batasan.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Stakeholder Utama -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fas fa-users text-sm"></i> {{ __('Stakeholder Utama') }}
                            </h3>
                            <div class="flex-grow">
                                <h4 class="text-xs font-semibold text-slate-400 mb-1.5">{{ __('Ringkasan Pemangku Kepentingan') }}</h4>
                                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100 text-sm text-slate-755 leading-relaxed whitespace-pre-wrap min-h-[175px] h-full">
                                    {{ $charter->stakeholder_summary ?: __('Tidak ada ringkasan pemangku kepentingan.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6: Catatan & Umpan Balik Manager -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-comment-dots text-sm"></i> {{ __('Catatan & Umpan Balik Manager') }}
                        </h3>
                        <div class="p-4 rounded-xl text-sm leading-relaxed border {{ $charter->feedback_notes ? 'bg-amber-50/40 border-amber-100 text-amber-900' : 'bg-slate-50/30 border-slate-100 text-slate-400 italic' }}">
                            @if($charter->feedback_notes)
                                <p class="whitespace-pre-wrap">{{ $charter->feedback_notes }}</p>
                            @else
                                <p>{{ __('Belum ada catatan atau umpan balik yang diberikan.') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Card 7: Rekomendasi AI Assistant (AI Suggestions) -->
                    @if($showAiSection)
                        <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                                <i class="fas fa-robot text-sm"></i>
                            </div>
                            
                            <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fas fa-magic"></i> {{ __('Rekomendasi AI Assistant (AI Suggestions)') }}
                            </h3>

                            @if($charter->ai_suggestions)
                                <div class="space-y-4">
                                    @if($isJsonSuggestions)
                                        <!-- Purpose Suggestion -->
                                        @if(isset($suggestions['project_purpose']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-blue-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-regular fa-lightbulb text-blue-600"></i> Saran Tujuan Proyek
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap animate-pulse-once" id="ai-suggest-project_purpose">{{ $suggestions['project_purpose'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_purpose').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Business Case Suggestion -->
                                        @if(isset($suggestions['business_case']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-indigo-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-chart-line text-indigo-600"></i> Saran Business Case
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-business_case">{{ $suggestions['business_case'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-business_case').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Objectives Suggestion -->
                                        @if(isset($suggestions['project_objectives']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-purple-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-bullseye text-purple-600"></i> Saran Sasaran Proyek
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-project_objectives">{{ $suggestions['project_objectives'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-project_objectives').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Scope Summary Suggestion -->
                                        @if(isset($suggestions['scope_summary']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-sky-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-compress-arrows-alt text-sky-600"></i> Saran Ruang Lingkup
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-scope_summary">{{ $suggestions['scope_summary'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-scope_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Success Criteria Suggestion -->
                                        @if(isset($suggestions['success_criteria']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-emerald-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-check-double text-emerald-600"></i> Saran Kriteria Sukses
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-success_criteria">{{ $suggestions['success_criteria'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-success_criteria').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Assumptions Suggestion -->
                                        @if(isset($suggestions['assumptions']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-teal-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-circle-question text-teal-600"></i> Saran Asumsi
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-assumptions">{{ $suggestions['assumptions'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-assumptions').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Constraints Suggestion -->
                                        @if(isset($suggestions['constraints']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-rose-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Saran Batasan
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-constraints">{{ $suggestions['constraints'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-constraints').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Stakeholder Suggestion -->
                                        @if(isset($suggestions['stakeholder_summary']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-violet-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-users text-violet-600"></i> Saran Pemangku Kepentingan
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-stakeholder_summary">{{ $suggestions['stakeholder_summary'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-stakeholder_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Milestone Suggestion -->
                                        @if(isset($suggestions['milestone_summary']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-pink-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-flag text-pink-600"></i> Saran Milestone
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-milestone_summary">{{ $suggestions['milestone_summary'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-milestone_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Budget Suggestion -->
                                        @if(isset($suggestions['budget_summary']))
                                            <div class="bg-white p-4 rounded-xl border-l-4 border-amber-500 border border-slate-100 shadow-sm space-y-2">
                                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="fa-regular fa-money-bill-1 text-amber-600"></i> Analisis Anggaran
                                                </span>
                                                <p class="text-xs text-slate-600 italic leading-relaxed whitespace-pre-wrap" id="ai-suggest-budget_summary">{{ $suggestions['budget_summary'] }}</p>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ai-suggest-budget_summary').innerText); alert('Salin berhasil!');" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 rounded-lg text-[10px] font-semibold transition">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <!-- Plain markdown display -->
                                        <div class="bg-indigo-50/20 p-6 rounded-xl border border-indigo-50/50 text-sm text-indigo-950 font-sans leading-relaxed">
                                            <div class="markdown-content" id="aiSuggestionsTextRaw">
                                                {!! str($charter->ai_suggestions)->markdown() !!}
                                            </div>
                                            <button onclick="navigator.clipboard.writeText(document.getElementById('aiSuggestionsTextRaw').innerText); alert('Salin berhasil!');" 
                                                    class="w-full mt-3 inline-flex items-center justify-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                                <i class="fas fa-copy mr-1"></i> {{ __('Salin Semua Rekomendasi') }}
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Regenerate button -->
                                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $charter->status === 'draft')
                                        <div class="mt-4 flex justify-end">
                                            <form action="{{ route('projects.charter.generate_ai', $project->id) }}" method="POST" class="ai-generate-form">
                                                @csrf
                                                <button type="submit" class="btn-ai-generate inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md transition duration-200 gap-1.5">
                                                    <i class="fas fa-sync-alt animate-icon"></i> {{ __('Regenerate Rekomendasi AI') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- Empty state generate button -->
                                <div class="border border-dashed border-indigo-100 bg-indigo-50/20 p-6 rounded-xl text-center">
                                    <p class="text-sm font-semibold text-indigo-950 mb-1">{{ __('Rekomendasi AI Belum Digenerate') }}</p>
                                    <p class="text-xs text-indigo-600/70 max-w-md mx-auto leading-relaxed mb-4">
                                        {{ __('AI Assistant dapat menganalisis deskripsi proyek dan proposal Anda untuk menghasilkan draf saran Project Charter yang relevan.') }}
                                    </p>
                                    
                                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $charter->status === 'draft')
                                        <form action="{{ route('projects.charter.generate_ai', $project->id) }}" method="POST" class="ai-generate-form">
                                            @csrf
                                            <button type="submit" class="btn-ai-generate inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-md transition duration-200 gap-1.5">
                                                <i class="fas fa-magic animate-icon"></i> {{ __('Generate Rekomendasi AI Sekarang') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-block text-xs font-medium text-gray-500 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2">
                                            {{ __('Regenerasi AI hanya aktif saat status draf.') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Right Column: Sidebar Metadata & Status Actions (1/3 Width) -->
                <div class="space-y-6">
                    <!-- Financial Box -->
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl text-white shadow-md relative overflow-hidden">
                        <!-- Decorative background circles -->
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                        <div class="absolute -left-6 -top-6 w-20 h-20 bg-white/15 rounded-full blur-lg"></div>

                        <span class="text-[9px] font-bold text-indigo-200 uppercase tracking-wider block mb-1">
                            <i class="fa-regular fa-money-bill-1 mr-1"></i>{{ __('Ringkasan Anggaran') }}
                        </span>
                        <div class="text-2xl font-black tracking-tight">
                            @if($charter->budget_summary !== null)
                                Rp {{ number_format($charter->budget_summary, 2, ',', '.') }}
                            @else
                                Rp -
                            @endif
                        </div>
                        <p class="text-[10px] text-indigo-100/80 mt-3 leading-relaxed">
                            {{ __('Anggaran definitif awal yang diusulkan dan disetujui dalam dokumen charter ini.') }}
                        </p>
                    </div>

                    <!-- Audit Metadata Box -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-1.5">
                            <i class="fa-regular fa-folder-open text-slate-400"></i> {{ __('Metadata Dokumen') }}
                        </h3>
                        <div class="space-y-4 text-xs">
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">{{ __('Dibuat Oleh') }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                        {{ $charter->creator ? strtoupper(substr($charter->creator->name, 0, 2)) : '-' }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 block">{{ $charter->creator ? $charter->creator->name : '-' }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $charter->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">{{ __('Pembaruan Terakhir') }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                        {{ $charter->updater ? strtoupper(substr($charter->updater->name, 0, 2)) : '-' }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 block">{{ $charter->updater ? $charter->updater->name : '-' }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $charter->updated_at->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions / Finalize Form Contextual -->
                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $charter->status === 'draft')
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-1.5">
                                <i class="fa-solid fa-gears text-slate-400"></i> {{ __('Aksi Manager') }}
                            </h3>
                            
                            <a href="{{ route('projects.charter.edit', $project->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition gap-1.5">
                                <i class="fas fa-edit"></i> {{ __('Ubah Project Charter') }}
                            </a>

                            <form action="{{ route('projects.charter.update', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi Project Charter ini? Setelah difinalisasi, Anda tidak dapat mengedit lagi.');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="submit">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition gap-1.5">
                                    <i class="fas fa-check-circle"></i> {{ __('Finalisasi Project Charter') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.ai-generate-form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    const btn = form.querySelector('.btn-ai-generate');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        const icon = btn.querySelector('.animate-icon');
                        if (icon) {
                            icon.className = 'fas fa-spinner fa-spin';
                        }
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> {{ __("Sedang Memproses AI...") }}';
                    }
                    
                    // Disable other buttons on the page to prevent multiple submissions
                    const allActionButtons = document.querySelectorAll('.btn-ai-generate, a, button[type="submit"]');
                    allActionButtons.forEach(actionBtn => {
                        if (actionBtn !== btn) {
                            if (actionBtn.tagName === 'A') {
                                actionBtn.classList.add('pointer-events-none', 'opacity-50');
                            } else {
                                actionBtn.disabled = true;
                                actionBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            }
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
