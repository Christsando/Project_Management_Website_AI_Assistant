<x-app-layout>
    <x-slot name="header">
        <x-header-component title="Detail Proposal Proyek" icon="fa-regular fa-file-lines text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        <!-- Back Link & Top Info -->
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition gap-1.5">
                <i class="fas fa-arrow-left"></i>
                {{ __('Kembali ke Detail Proyek') }}
            </a>
            
            @if($proposal)
                <div>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-gray-50 text-gray-700 border-gray-200',
                            'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'reviewed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'revision_needed' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ][$proposal->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses }} shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ __('Status: ') . $proposal->status }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                <span class="font-semibold">{{ session('info') }}</span>
            </div>
        @endif

        @if(!$proposal)
            <!-- Empty State -->
            <div class="bg-white p-16 rounded-2xl border border-slate-100 shadow-sm text-center max-w-2xl mx-auto my-12">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 border border-blue-100 shadow-sm">
                    <i class="fa-regular fa-file-lines text-2xl"></i>
                </div>
                <h4 class="font-extrabold text-xl text-slate-800 mb-2">{{ __('Proposal Belum Dibuat') }}</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
                    {{ __('Latar belakang, tujuan, perkiraan anggaran, dan dokumen inisiasi proposal lainnya belum didefinisikan untuk proyek ini.') }}
                </p>

                @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved')
                    <a href="{{ route('projects.proposal.create', $project->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                        <i class="fas fa-plus text-[10px]"></i>
                        {{ __('Buat Proposal Sekarang') }}
                    </a>
                @else
                    <span class="inline-block text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 rounded-xl px-4 py-2.5">
                        <i class="fas fa-exclamation-triangle mr-1.5"></i> {{ __('Proposal belum dibuat oleh Manager.') }}
                    </span>
                @endif
            </div>
        @else
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- Left Column: Details (2/3 Width) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Latar Belakang Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-history text-blue-500 text-[10px]"></i> {{ __('Latar Belakang') }}
                        </h3>
                        <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $proposal->background ?: __('Tidak ada detail latar belakang.') }}
                        </div>
                    </div>

                    <!-- Tujuan Proyek Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-bullseye text-indigo-500 text-[10px]"></i> {{ __('Tujuan Proyek (Objectives)') }}
                        </h3>
                        <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $proposal->objectives ?: __('Tidak ada detail tujuan proyek.') }}
                        </div>
                    </div>

                    <!-- Kebutuhan Awal Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-briefcase text-purple-500 text-[10px]"></i> {{ __('Kebutuhan Awal') }}
                        </h3>
                        <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $proposal->initial_needs ?: __('Tidak ada detail kebutuhan awal.') }}
                        </div>
                    </div>

                    <!-- Gambaran Umum Proyek Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-globe text-sky-500 text-[10px]"></i> {{ __('Gambaran Umum Proyek') }}
                        </h3>
                        <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $proposal->project_overview ?: __('Tidak ada gambaran umum proyek.') }}
                        </div>
                    </div>

                    <!-- Gambaran Ruang Lingkup Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fas fa-compress-arrows-alt text-amber-500 text-[10px]"></i> {{ __('Gambaran Ruang Lingkup (Scope)') }}
                        </h3>
                        <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $proposal->scope_overview ?: __('Tidak ada gambaran ruang lingkup.') }}
                        </div>
                    </div>

                    <!-- Catatan & Umpan Balik Manager -->
                    @if($proposal->feedback_notes)
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-amber-500">
                            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fas fa-comment-dots text-[10px]"></i> {{ __('Catatan & Umpan Balik Manager') }}
                            </h3>
                            <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                                {{ $proposal->feedback_notes }}
                            </div>
                        </div>
                    @endif

                    <!-- AI Suggestions Panel -->
                    @if(strtolower(Auth::user()->role) === 'manager')
                        <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 bg-indigo-50 text-indigo-500 rounded-bl-2xl">
                                <i class="fas fa-robot text-sm animate-bounce"></i>
                            </div>
                            <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fas fa-magic"></i> {{ __('Rekomendasi & Analisis AI (AI Suggestions)') }}
                            </h3>

                            @if($proposal->ai_suggestions)
                                @php
                                    $decoded = json_decode($proposal->ai_suggestions, true);
                                    $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($decoded));
                                @endphp

                                <div class="bg-indigo-50/20 p-6 rounded-xl border border-indigo-50 text-sm text-indigo-950 font-sans leading-relaxed">
                                    @if($isJson)
                                        <div class="space-y-4">
                                            @foreach($decoded as $key => $val)
                                                <div class="border-b border-indigo-100/40 pb-3 last:border-0 last:pb-0">
                                                    <span class="font-bold text-xs uppercase text-indigo-600 block mb-1">
                                                        {{ str_replace('_', ' ', $key) }}
                                                    </span>
                                                    <p class="whitespace-pre-wrap text-slate-700 text-xs">{{ $val }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="markdown-content text-xs text-slate-700">
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
                                <div class="border border-dashed border-indigo-100 bg-indigo-50/20 p-8 rounded-xl text-center">
                                    <p class="text-sm font-bold text-indigo-950 mb-1.5">{{ __('Rekomendasi AI Belum Digenerate') }}</p>
                                    <p class="text-xs text-indigo-600/70 max-w-md mx-auto leading-relaxed mb-5">
                                        {{ __('AI Assistant dapat menganalisis deskripsi proyek untuk menghasilkan draf saran Project Proposal yang relevan.') }}
                                    </p>
                                    
                                    @if($project->status === 'approved' && $proposal->status === 'draft')
                                        <form action="{{ route('projects.proposal.generate_ai', $project->id) }}" method="POST" class="ai-generate-form">
                                            @csrf
                                            <button type="submit" class="btn-ai-generate inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md transition duration-200 gap-1.5">
                                                <i class="fas fa-magic animate-icon"></i> {{ __('Generate Rekomendasi AI Sekarang') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-block text-xs font-medium text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                                            {{ __('Regenerasi AI hanya aktif saat status draf.') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Right Column: Sidebar (1/3 Width) -->
                <div class="space-y-6">
                    <!-- Financial Box -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-2xl text-white shadow-md relative overflow-hidden">
                        <!-- Decorative background SVG shapes -->
                        <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none">
                            <i class="fas fa-wallet text-9xl"></i>
                        </div>
                        
                        <h3 class="text-xs font-semibold text-blue-100 uppercase tracking-wider mb-1">{{ __('Perkiraan Anggaran') }}</h3>
                        <div class="text-2xl font-black tracking-tight">
                            @if($proposal->estimated_budget !== null)
                                Rp {{ number_format($proposal->estimated_budget, 2, ',', '.') }}
                            @else
                                Rp -
                            @endif
                        </div>
                        <p class="text-[10px] text-blue-200/80 mt-3 leading-relaxed">
                            {{ __('Anggaran indikatif awal untuk implementasi dan alokasi sumber daya.') }}
                        </p>
                    </div>

                    <!-- Audit Metadata Box -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4 text-xs">
                        <h3 class="font-bold text-slate-800 pb-2 border-b border-slate-100">{{ __('Metadata Dokumen') }}</h3>
                        <div class="space-y-3.5">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 text-xs shadow-sm">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] font-semibold uppercase tracking-wider">{{ __('Dibuat Oleh:') }}</span>
                                    <span class="font-bold text-slate-800 block mt-0.5">{{ $proposal->creator ? $proposal->creator->name : '-' }}</span>
                                    <span class="text-slate-400 block text-[9px] mt-0.5"><i class="fa-regular fa-clock mr-1"></i>{{ $proposal->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 text-xs shadow-sm">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] font-semibold uppercase tracking-wider">{{ __('Pembaruan Terakhir:') }}</span>
                                    <span class="font-bold text-slate-800 block mt-0.5">{{ $proposal->updater ? $proposal->updater->name : '-' }}</span>
                                    <span class="text-slate-400 block text-[9px] mt-0.5"><i class="fa-regular fa-clock mr-1"></i>{{ $proposal->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions / Finalize Form Contextual -->
                    @if(strtolower(Auth::user()->role) === 'manager' && $project->status === 'approved' && $proposal->status === 'draft')
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                            <h3 class="font-bold text-slate-800 pb-2 border-b border-slate-100 text-xs">{{ __('Aksi Manager') }}</h3>
                            
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

    <!-- JS Loader Helper -->
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
