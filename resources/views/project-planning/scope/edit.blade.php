<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.scope.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Detail Scope') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Ubah Project Scope') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                <form action="{{ route('projects.scope.update', $project->id) }}" method="POST" id="scopeForm">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: Objectives & Description -->
                    <div class="border-b border-gray-100 pb-6 mb-6">
                        <h4 class="text-sm font-bold text-primaryText uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                            {{ __('Tujuan & Deskripsi Proyek') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="objective" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tujuan Proyek (Objective) *') }}</label>
                                <textarea name="objective" id="objective" rows="4" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Jelaskan tujuan akhir proyek secara spesifik dan terukur...">{{ old('objective', $scope->objective) }}</textarea>
                                @error('objective')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="scope_description" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Deskripsi Ruang Lingkup *') }}</label>
                                <textarea name="scope_description" id="scope_description" rows="4" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Jelaskan ringkasan cakupan pekerjaan proyek secara umum...">{{ old('scope_description', $scope->scope_description) }}</textarea>
                                @error('scope_description')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: In Scope vs Out of Scope -->
                    <div class="border-b border-gray-100 pb-6 mb-6">
                        <h4 class="text-sm font-bold text-primaryText uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">2</span>
                            {{ __('Batasan Ruang Lingkup (In-Scope / Out-of-Scope)') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="in_scope" class="block text-sm font-semibold text-primaryText mb-1.5 text-emerald-700">{{ __('Pekerjaan yang Termasuk (In-Scope) *') }}</label>
                                <textarea name="in_scope" id="in_scope" rows="5" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-emerald-500 focus:border-emerald-500 transition" 
                                          placeholder="Tuliskan daftar modul, fitur, atau pekerjaan yang masuk dalam proyek (pisahkan dengan baris baru atau poin)...">{{ old('in_scope', $scope->in_scope) }}</textarea>
                                @error('in_scope')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="out_of_scope" class="block text-sm font-semibold text-primaryText mb-1.5 text-rose-700">{{ __('Pekerjaan yang Tidak Termasuk (Out-of-Scope) *') }}</label>
                                <textarea name="out_of_scope" id="out_of_scope" rows="5" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-rose-500 focus:border-rose-500 transition" 
                                          placeholder="Tuliskan apa saja yang tidak akan dikerjakan dalam proyek ini agar tidak terjadi scope creep...">{{ old('out_of_scope', $scope->out_of_scope) }}</textarea>
                                @error('out_of_scope')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Deliverables & Acceptance Criteria -->
                    <div class="border-b border-gray-100 pb-6 mb-6">
                        <h4 class="text-sm font-bold text-primaryText uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">3</span>
                            {{ __('Hasil Kerja & Kriteria Penerimaan') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="deliverables" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Hasil Kerja (Deliverables) *') }}</label>
                                <textarea name="deliverables" id="deliverables" rows="4" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Sebutkan hasil fisik/non-fisik yang harus diserahkan (misal: dokumen, source code, laporan)...">{{ old('deliverables', $scope->deliverables) }}</textarea>
                                @error('deliverables')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="acceptance_criteria" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Kriteria Penerimaan (Acceptance Criteria) *') }}</label>
                                <textarea name="acceptance_criteria" id="acceptance_criteria" rows="4" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Sebutkan standar atau syarat yang harus dipenuhi agar hasil kerja dapat diterima oleh klien/user...">{{ old('acceptance_criteria', $scope->acceptance_criteria) }}</textarea>
                                @error('acceptance_criteria')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Optional Project Parameters -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-primaryText uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">4</span>
                            {{ __('Persyaratan Utama, Asumsi, & Batasan') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="main_requirements" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Persyaratan Utama (Optional)') }}</label>
                                <textarea name="main_requirements" id="main_requirements" rows="3" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Sebutkan kebutuhan fungsional/non-fungsional utama...">{{ old('main_requirements', $scope->main_requirements) }}</textarea>
                                @error('main_requirements')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="assumptions" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Asumsi (Optional)') }}</label>
                                <textarea name="assumptions" id="assumptions" rows="3" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Asumsi-asumsi yang dianggap benar selama siklus perencanaan proyek...">{{ old('assumptions', $scope->assumptions) }}</textarea>
                                @error('assumptions')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="constraints" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Batasan / Kendala (Optional)') }}</label>
                                <textarea name="constraints" id="constraints" rows="3" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Faktor pembatas (misal: budget, resource, regulasi, waktu)...">{{ old('constraints', $scope->constraints) }}</textarea>
                                @error('constraints')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Catatan Tambahan (Optional)') }}</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                          placeholder="Catatan tambahan penting lainnya terkait ruang lingkup proyek...">{{ old('notes', $scope->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                        <a href="{{ route('projects.scope.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" name="action" value="save" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition">
                            {{ __('Simpan Draf') }}
                        </button>
                        <button type="submit" name="action" value="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition" onclick="return confirm('Apakah Anda yakin ingin memfinalisasi Project Scope ini? Data yang telah difinalisasi tidak dapat diubah lagi.');">
                            {{ __('Finalisasi Scope') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
