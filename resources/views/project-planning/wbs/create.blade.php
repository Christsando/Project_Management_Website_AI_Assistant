<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.wbs.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke WBS Proyek') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Tambah Item WBS Baru') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                <form action="{{ route('projects.wbs.store', $project->id) }}" method="POST" id="wbsForm">
                    @csrf

                    <div class="space-y-6">
                        <!-- WBS Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Judul / Nama Tugas *') }}</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                   placeholder="Tuliskan judul tugas atau bagian kerja...">
                            @error('title')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- WBS Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Deskripsi Pekerjaan *') }}</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Jelaskan secara detail apa yang akan dikerjakan pada tugas ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deliverable -->
                        <div>
                            <label for="deliverable" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Hasil Kerja / Deliverable (Optional)') }}</label>
                            <input type="text" name="deliverable" id="deliverable" value="{{ old('deliverable') }}"
                                   class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                   placeholder="Hasil akhir fisik atau dokumen dari tugas ini (misal: dokumen SRS, modul login, database schema)...">
                            @error('deliverable')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Prioritas Tugas *') }}</label>
                                <select name="priority" id="priority" 
                                        class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                    <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="high" {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                </select>
                                @error('priority')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estimated Duration -->
                            <div>
                                <label for="estimated_duration_days" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Estimasi Durasi (Hari) (Optional)') }}</label>
                                <input type="number" name="estimated_duration_days" id="estimated_duration_days" min="1" value="{{ old('estimated_duration_days') }}"
                                       class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                       placeholder="Jumlah hari yang dibutuhkan (misal: 5)">
                                @error('estimated_duration_days')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Parent WBS Item (Hierarchy) -->
                        <div>
                            <label for="parent_id" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Parent Tugas / Sub-Task Dari (Optional)') }}</label>
                            <select name="parent_id" id="parent_id" 
                                    class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                <option value="">-- {{ __('Tugas Utama / Root Task (Tanpa Parent)') }} --</option>
                                @foreach($parentItems as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->title }} (ID: #{{ $parent->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                        <a href="{{ route('projects.wbs.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition">
                            {{ __('Tambah Item') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
