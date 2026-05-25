<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-3xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="mb-6 border-b border-slate-50 pb-5">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                    {{ __('Inisialisasi Budget Plan') }}
                </h2>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-400 font-medium">
                    <span>{{ __('Proyek:') }}</span>
                    <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $project->title }}</span>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white">
                <form action="{{ route('projects.budget.store', $project->id) }}" method="POST">
                    @csrf

                    <!-- Info Alert -->
                    <div class="mb-6 p-5 bg-blue-50 border border-blue-100 text-blue-800 rounded-2xl flex items-start gap-4 shadow-sm">
                        <div class="w-10 h-10 bg-blue-600/10 text-blue-600 rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-slate-800">{{ __('Informasi Inisialisasi:') }}</span>
                            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed font-semibold">
                                {{ __('Inisialisasi ini akan membuat draf Rencana Anggaran Belanja (RAB) baru untuk proyek ini. Setelah diinisialisasi, Anda dapat menambahkan rincian item anggaran seperti kebutuhan tim pelaksana (HR), software pendukung, infrastruktur server, dan biaya operasional lainnya.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            {{ __('Catatan Rencana Anggaran (Opsional)') }}
                        </label>
                        <textarea name="notes" id="notes" rows="4" 
                                  class="w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-xs font-semibold"
                                  placeholder="Tuliskan catatan umum atau asumsi dasar penyusunan anggaran belanja proyek di sini...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-50">
                        <a href="{{ route('project-planning.budget.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition flex items-center gap-1.5">
                            <i class="fas fa-check text-[10px]"></i>
                            {{ __('Inisialisasi RAB') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
