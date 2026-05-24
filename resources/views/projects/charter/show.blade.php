<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Detail Proyek') }}
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                            {{ __('Project Charter') }}
                        </h2>
                        <h3 class="text-sm text-secondaryText mt-1">
                            {{ __('Proyek:') }} <span class="font-semibold text-primaryText">{{ $project->title }}</span>
                        </h3>
                    </div>

                    @if($charter)
                        <div>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'submitted' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'reviewed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'revision_needed' => 'bg-rose-50 text-rose-800 border-rose-200',
                                ][$charter->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-semibold border {{ $statusClasses }} shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ __('Status Charter: ') . ucfirst($charter->status) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

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

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(!$charter)
                <!-- Empty State -->
                <div class="bg-white p-12 rounded-2xl border border-[#e3e3e0] shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100">
                        <i class="fas fa-file-signature text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-primaryText mb-1">{{ __('Project Charter Belum Dibuat') }}</h4>
                    <p class="text-sm text-secondaryText max-w-md mx-auto mb-6">
                        {{ __('Project Charter mendefinisikan tujuan proyek, kasus bisnis, batasan, milestone, anggaran, dan pemangku kepentingan kunci.') }}
                    </p>

                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                        <a href="{{ route('projects.charter.create', $project->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            {{ __('Buat Project Charter Sekarang') }}
                        </a>
                    @else
                        <span class="inline-block text-xs font-medium text-amber-600 bg-amber-50 border border-amber-100 rounded-lg px-4 py-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Project Charter belum dibuat oleh Manager.') }}
                        </span>
                    @endif
                </div>
            @else
                <!-- Charter Details Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main details (Left 2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-6">
                            
                            <!-- Project Purpose & Business Case Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-lightbulb text-gray-400"></i> {{ __('Tujuan Proyek (Project Purpose)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->project_purpose ?: __('Tidak ada detail tujuan proyek.') }}
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-chart-line text-gray-400"></i> {{ __('Kasus Bisnis (Business Case)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->business_case ?: __('Tidak ada detail kasus bisnis.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Objectives & Scope Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-bullseye text-gray-400"></i> {{ __('Sasaran Proyek (Project Objectives)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->project_objectives ?: __('Tidak ada detail sasaran proyek.') }}
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-compress-arrows-alt text-gray-400"></i> {{ __('Ringkasan Ruang Lingkup (Scope Summary)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->scope_summary ?: __('Tidak ada ringkasan ruang lingkup.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Success Criteria & Stakeholders -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-check-double text-gray-400"></i> {{ __('Kriteria Keberhasilan') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->success_criteria ?: __('Tidak ada kriteria keberhasilan.') }}
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-users text-gray-400"></i> {{ __('Ringkasan Pemangku Kepentingan') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->stakeholder_summary ?: __('Tidak ada ringkasan pemangku kepentingan.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Assumptions & Constraints -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-question-circle text-gray-400"></i> {{ __('Asumsi (Assumptions)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->assumptions ?: __('Tidak ada detail asumsi.') }}
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-exclamation-triangle text-gray-400"></i> {{ __('Batasan (Constraints)') }}
                                    </h3>
                                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                        {{ $charter->constraints ?: __('Tidak ada detail batasan.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Milestones -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-flag text-gray-400"></i> {{ __('Ringkasan Milestone') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $charter->milestone_summary ?: __('Tidak ada ringkasan milestone.') }}
                                </div>
                            </div>

                        </div>

                        <!-- Feedback Notes (Only if exists) -->
                        <div class="bg-white p-6 rounded-2xl border border-[#e3e3e0] shadow-sm">
                            <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fas fa-comment-dots text-gray-400"></i> {{ __('Catatan & Umpan Balik Manager') }}
                            </h3>
                            <div class="bg-amber-50/30 p-4 rounded-xl border border-amber-100/60 text-sm text-primaryText leading-relaxed">
                                @if($charter->feedback_notes)
                                    <p class="whitespace-pre-line text-amber-900">{{ $charter->feedback_notes }}</p>
                                @else
                                    <p class="text-gray-400 italic">{{ __('Belum ada catatan atau umpan balik yang diberikan.') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- AI Suggestions Section -->
                        @php
                            $userRole = strtolower(Auth::user()->role);
                            $showAiSection = ($userRole === 'manager');
                        @endphp

                        @if($showAiSection)
                            <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                                    <i class="fas fa-robot text-sm"></i>
                                </div>
                                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-magic"></i> {{ __('Rekomendasi AI Assistant (AI Suggestions)') }}
                                </h3>

                                @if($charter->ai_suggestions)
                                    <div class="bg-indigo-50/20 p-6 rounded-xl border border-indigo-50/50 text-sm text-indigo-950 font-sans leading-relaxed">
                                        <div class="markdown-content">
                                            {!! str($charter->ai_suggestions)->markdown() !!}
                                        </div>
                                    </div>

                                    <!-- Manager can regenerate if states allow -->
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
                                @else
                                    <div class="border-2 border-dashed border-indigo-100 bg-indigo-50/20 p-6 rounded-xl text-center">
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

                    <!-- Sidebar Metadata & Status Actions (Right 1 col) -->
                    <div class="space-y-6">
                        <!-- Financial Box -->
                        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl text-white shadow-md">
                            <h3 class="text-xs font-semibold text-indigo-100 uppercase tracking-wider mb-1">{{ __('Ringkasan Anggaran') }}</h3>
                            <div class="text-2xl font-black">
                                @if($charter->budget_summary !== null)
                                    Rp {{ number_format($charter->budget_summary, 2, ',', '.') }}
                                @else
                                    Rp -
                                @endif
                            </div>
                            <p class="text-[10px] text-indigo-200/80 mt-2 leading-relaxed">
                                {{ __('Anggaran definitif awal yang diusulkan dan disetujui dalam dokumen charter ini.') }}
                            </p>
                        </div>

                        <!-- Audit Metadata Box -->
                        <div class="bg-white p-5 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-4 text-xs">
                            <h3 class="font-bold text-primaryText pb-2 border-b border-gray-100">{{ __('Metadata Dokumen') }}</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-secondaryText block">{{ __('Dibuat Oleh:') }}</span>
                                    <span class="font-semibold text-primaryText">{{ $charter->creator ? $charter->creator->name : '-' }}</span>
                                    <span class="text-gray-400 block text-[10px]">{{ $charter->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="text-secondaryText block">{{ __('Pembaruan Terakhir:') }}</span>
                                    <span class="font-semibold text-primaryText">{{ $charter->updater ? $charter->updater->name : '-' }}</span>
                                    <span class="text-gray-400 block text-[10px]">{{ $charter->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions / Finalize Form Contextual -->
                        @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $charter->status === 'draft')
                            <div class="bg-white p-5 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-3">
                                <h3 class="font-bold text-primaryText pb-2 border-b border-gray-100 text-xs">{{ __('Aksi Manager') }}</h3>
                                
                                <a href="{{ route('projects.charter.edit', $project->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition duration-200 gap-1.5">
                                    <i class="fas fa-edit"></i> {{ __('Ubah Project Charter') }}
                                </a>

                                <form action="{{ route('projects.charter.update', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi Project Charter ini? Setelah difinalisasi, Anda tidak dapat mengedit lagi.');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="submit">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition duration-200 gap-1.5">
                                        <i class="fas fa-check-circle"></i> {{ __('Finalisasi Project Charter') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
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
