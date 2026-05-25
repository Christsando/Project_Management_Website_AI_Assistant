<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-2 pb-12">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-3xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.human-resource.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Inisialisasi Human Resource Plan') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Inisialisasi ini akan membuat draf Perencanaan Sumber Daya Manusia (SDM) baru untuk proyek ini.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-400 font-medium">
                        <span>{{ __('Proyek:') }}</span>
                        <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $project->title }}</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('projects.human-resource.store', $project->id) }}" method="POST">
                @csrf

                <!-- Info Alert Card -->
                <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">{{ __('Informasi Perencanaan SDM') }}</h4>
                        <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                            {{ __('Setelah diinisialisasi, Anda dapat menambahkan rincian kebutuhan tim pelaksana proyek, mendefinisikan kriteria skill, deskripsi tugas pekerjaan, dan menetapkan penanggung jawab (PIC) langsung terhadap struktur kerja (WBS) yang ada.') }}
                        </p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        {{ __('Catatan Perencanaan SDM (Opsional)') }}
                    </label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 placeholder-slate-400 bg-slate-50/50"
                              placeholder="Tuliskan catatan umum atau asumsi dasar perencanaan alokasi SDM proyek di sini...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-rose-600 text-xs mt-1.5 font-bold flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('project-planning.human-resource.index') }}" class="px-4 py-2 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-check"></i>
                        {{ __('Inisialisasi HR Plan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
