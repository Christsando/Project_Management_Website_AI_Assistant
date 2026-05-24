<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('project-planning.risk-management.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Inisialisasi Risk Management Plan') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                <form action="{{ route('projects.risk-management.store', $project->id) }}" method="POST">
                    @csrf

                    <!-- Info Alert -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div>
                            <span class="font-bold">{{ __('Informasi Inisialisasi:') }}</span>
                            <p class="mt-0.5 text-secondaryText leading-relaxed">
                                {{ __('Inisialisasi ini akan membuat draf Rencana Manajemen Risiko baru untuk proyek ini. Setelah diinisialisasi, Anda (PMO) dapat mulai memetakan potensi risiko proyek, menetapkan peluang (probability), keparahan (severity), pemilik risiko (risk owner), menautkannya dengan tugas WBS, serta memanfaatkan AI Assistant untuk memberikan rekomendasi risiko awal berdasarkan dokumen proyek yang sudah diselesaikan.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-semibold text-primaryText mb-1.5">
                            {{ __('Catatan Rencana Manajemen Risiko (Opsional)') }}
                        </label>
                        <textarea name="notes" id="notes" rows="4" 
                                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 text-sm"
                                  placeholder="Tuliskan catatan umum atau filosofi manajemen risiko untuk proyek ini di sini...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('project-planning.risk-management.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-semibold transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition flex items-center gap-1.5">
                            <i class="fas fa-check"></i>
                            {{ __('Inisialisasi Risk Plan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
