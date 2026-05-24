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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                            {{ __('Project Proposal') }}
                        </h2>
                        <h3 class="text-sm text-secondaryText mt-1">
                            {{ __('Proyek:') }} <span class="font-semibold text-primaryText">{{ $project->title }}</span>
                        </h3>
                    </div>

                    @if($proposal)
                        <div>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'submitted' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'reviewed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'revision_needed' => 'bg-rose-50 text-rose-800 border-rose-200',
                                ][$proposal->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-semibold border {{ $statusClasses }} shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ __('Status Proposal: ') . ucfirst($proposal->status) }}
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

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(!$proposal)
                <!-- Empty State -->
                <div class="bg-white p-12 rounded-2xl border border-[#e3e3e0] shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100">
                        <i class="fas fa-file-invoice text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-lg text-primaryText mb-1">{{ __('Proposal Belum Dibuat') }}</h4>
                    <p class="text-sm text-secondaryText max-w-md mx-auto mb-6">
                        {{ __('Latar belakang, tujuan, perkiraan anggaran, dan dokumen inisiasi proposal lainnya belum didefinisikan untuk proyek ini.') }}
                    </p>

                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                        <a href="{{ route('projects.proposal.create', $project->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            {{ __('Buat Proposal Sekarang') }}
                        </a>
                    @else
                        <span class="inline-block text-xs font-medium text-amber-600 bg-amber-50 border border-amber-100 rounded-lg px-4 py-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Proposal belum dibuat oleh Manager.') }}
                        </span>
                    @endif
                </div>
            @else
                <!-- Proposal Details Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main details (Left 2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Sections -->
                        <div class="bg-white p-6 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-6">
                            <!-- Background Section -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-history text-gray-400"></i> {{ __('Latar Belakang') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $proposal->background ?: __('Tidak ada detail latar belakang.') }}
                                </div>
                            </div>

                            <!-- Objectives Section -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-bullseye text-gray-400"></i> {{ __('Tujuan Proyek (Objectives)') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $proposal->objectives ?: __('Tidak ada detail tujuan proyek.') }}
                                </div>
                            </div>

                            <!-- Initial Needs Section -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-briefcase text-gray-400"></i> {{ __('Kebutuhan Awal') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $proposal->initial_needs ?: __('Tidak ada detail kebutuhan awal.') }}
                                </div>
                            </div>

                            <!-- Project Overview Section -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-globe text-gray-400"></i> {{ __('Gambaran Umum Proyek') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $proposal->project_overview ?: __('Tidak ada gambaran umum proyek.') }}
                                </div>
                            </div>

                            <!-- Scope Overview Section -->
                            <div>
                                <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-compress-arrows-alt text-gray-400"></i> {{ __('Gambaran Ruang Lingkup (Scope)') }}
                                </h3>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-sm text-primaryText whitespace-pre-line leading-relaxed">
                                    {{ $proposal->scope_overview ?: __('Tidak ada gambaran ruang lingkup.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Notes (Only if exists) -->
                        <div class="bg-white p-6 rounded-2xl border border-[#e3e3e0] shadow-sm">
                            <h3 class="text-xs font-bold text-secondaryText uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fas fa-comment-dots text-gray-400"></i> {{ __('Catatan & Umpan Balik Manager') }}
                            </h3>
                            <div class="bg-amber-50/30 p-4 rounded-xl border border-amber-100/60 text-sm text-primaryText leading-relaxed">
                                @if($proposal->feedback_notes)
                                    <p class="whitespace-pre-line text-amber-900">{{ $proposal->feedback_notes }}</p>
                                @else
                                    <p class="text-gray-400 italic">{{ __('Belum ada catatan atau umpan balik yang diberikan.') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- AI Suggestions Section -->
                        @if(strtolower(Auth::user()->role) === 'manager')
                            <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                                    <i class="fas fa-robot text-sm"></i>
                                </div>
                                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-magic"></i> {{ __('Rekomendasi & Analisis AI (AI Suggestions)') }}
                                </h3>

                                @if($proposal->ai_suggestions)
                                    @php
                                        $decoded = json_decode($proposal->ai_suggestions, true);
                                        $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($decoded));
                                    @endphp

                                    <div class="bg-indigo-50/20 p-6 rounded-xl border border-indigo-50/50 text-sm text-indigo-950 font-sans leading-relaxed">
                                        @if($isJson)
                                            <div class="space-y-4">
                                                @foreach($decoded as $key => $val)
                                                    <div class="border-b border-indigo-100/40 pb-3 last:border-0 last:pb-0">
                                                        <span class="font-bold text-xs uppercase text-indigo-600 block mb-1">
                                                            {{ str_replace('_', ' ', $key) }}
                                                        </span>
                                                        <p class="whitespace-pre-wrap">{{ $val }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="markdown-content">
                                                {!! str($proposal->ai_suggestions)->markdown() !!}
                                            </div>
                                        @endif
                                    </div>

                                    @if($project->status === 'approved' && $proposal->status === 'draft')
                                        <div class="mt-4 flex justify-end">
                                            <form action="{{ route('projects.proposal.generate_ai', $project->id) }}" method="POST" class="ai-generate-form">
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
                                            {{ __('AI Assistant dapat menganalisis deskripsi proyek untuk menghasilkan draf saran Project Proposal yang relevan.') }}
                                        </p>
                                        
                                        @if($project->status === 'approved' && $proposal->status === 'draft')
                                            <form action="{{ route('projects.proposal.generate_ai', $project->id) }}" method="POST" class="ai-generate-form">
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
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-2xl text-white shadow-md">
                            <h3 class="text-xs font-semibold text-blue-100 uppercase tracking-wider mb-1">{{ __('Perkiraan Anggaran') }}</h3>
                            <div class="text-2xl font-black">
                                @if($proposal->estimated_budget !== null)
                                    Rp {{ number_format($proposal->estimated_budget, 2, ',', '.') }}
                                @else
                                    Rp -
                                @endif
                            </div>
                            <p class="text-[10px] text-blue-200/80 mt-2 leading-relaxed">
                                {{ __('Anggaran indikatif awal untuk implementasi dan alokasi sumber daya.') }}
                            </p>
                        </div>

                        <!-- Audit Metadata Box -->
                        <div class="bg-white p-5 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-4 text-xs">
                            <h3 class="font-bold text-primaryText pb-2 border-b border-gray-100">{{ __('Metadata Dokumen') }}</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-secondaryText block">{{ __('Dibuat Oleh:') }}</span>
                                    <span class="font-semibold text-primaryText">{{ $proposal->creator ? $proposal->creator->name : '-' }}</span>
                                    <span class="text-gray-400 block text-[10px]">{{ $proposal->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="text-secondaryText block">{{ __('Pembaruan Terakhir:') }}</span>
                                    <span class="font-semibold text-primaryText">{{ $proposal->updater ? $proposal->updater->name : '-' }}</span>
                                    <span class="text-gray-400 block text-[10px]">{{ $proposal->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions / Finalize Form Contextual -->
                        @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $proposal->status === 'draft')
                            <div class="bg-white p-5 rounded-2xl border border-[#e3e3e0] shadow-sm space-y-3">
                                <h3 class="font-bold text-primaryText pb-2 border-b border-gray-100 text-xs">{{ __('Aksi Manager') }}</h3>
                                
                                <a href="{{ route('projects.proposal.edit', $project->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition duration-200 gap-1.5">
                                    <i class="fas fa-edit"></i> {{ __('Ubah Proposal') }}
                                </a>

                                <form action="{{ route('projects.proposal.update', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi proposal ini? Setelah difinalisasi, Anda tidak dapat mengedit lagi.');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="submit">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition duration-200 gap-1.5">
                                        <i class="fas fa-check-circle"></i> {{ __('Finalisasi Proposal') }}
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
