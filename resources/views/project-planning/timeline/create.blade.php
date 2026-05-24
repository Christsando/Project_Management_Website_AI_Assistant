<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6">
                <a href="{{ route('projects.timeline.show', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Timeline Proyek') }}
                </a>
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Tambah Jadwal Timeline Baru') }}
                </h2>
                <h3 class="text-sm text-secondaryText mt-1">
                    {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                </h3>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-xl border border-[#e3e3e0] shadow-sm">
                <form action="{{ route('projects.timeline.store', $project->id) }}" method="POST" id="timelineForm">
                    @csrf

                    <div class="space-y-6">
                        <!-- WBS Item Selection -->
                        <div>
                            <label for="wbs_item_id" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Pilih Item WBS *') }}</label>
                            <select name="wbs_item_id" id="wbs_item_id" 
                                    class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                <option value="">-- {{ __('Pilih Item WBS yang akan dijadwalkan') }} --</option>
                                @foreach($wbsItems as $wbs)
                                    <option value="{{ $wbs->id }}" {{ old('wbs_item_id', request('wbs_id')) == $wbs->id ? 'selected' : '' }}>
                                        {{ $wbs->title }} (ID: #{{ $wbs->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('wbs_item_id')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date range grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tanggal Mulai *') }}</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                       class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                @error('start_date')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tanggal Selesai *') }}</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                       class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                @error('end_date')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Predecessor/Dependency Selection -->
                        <div>
                            <label for="dependency_wbs_item_id" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Tugas Prasyarat / Predecessor (Optional)') }}</label>
                            <select name="dependency_wbs_item_id" id="dependency_wbs_item_id" 
                                    class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                <option value="">-- {{ __('Pilih tugas yang harus selesai sebelum tugas ini dimulai') }} --</option>
                                @foreach($dependencyItems as $dep)
                                    <option value="{{ $dep->id }}" {{ old('dependency_wbs_item_id') == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->title }} (Selesai: {{ $dep->timelineItem ? $dep->timelineItem->end_date->format('d-m-Y') : '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-secondaryText mt-1">{{ __('Catatan: Tanggal mulai tugas ini tidak boleh lebih cepat dari tanggal selesai tugas prasyarat.') }}</p>
                            @error('dependency_wbs_item_id')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Milestone Trigger -->
                        <div>
                            <label for="is_milestone" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Apakah ini Milestone? *') }}</label>
                            <select name="is_milestone" id="is_milestone" 
                                    class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition">
                                <option value="0" {{ old('is_milestone', '0') === '0' ? 'selected' : '' }}>{{ __('Bukan Milestone') }}</option>
                                <option value="1" {{ old('is_milestone', '0') === '1' ? 'selected' : '' }}>{{ __('Ya, ini Milestone') }}</option>
                            </select>
                            @error('is_milestone')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Milestone Name (Conditionally Displayed) -->
                        <div id="milestone_name_group" class="{{ old('is_milestone', '0') === '1' ? '' : 'hidden' }}">
                            <label for="milestone_name" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Nama Milestone *') }}</label>
                            <input type="text" name="milestone_name" id="milestone_name" value="{{ old('milestone_name') }}"
                                   class="w-full px-4 py-2.5 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                   placeholder="Tuliskan nama pencapaian utama/milestone (misal: Rilis Alpha, UAT Selesai)...">
                            @error('milestone_name')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-primaryText mb-1.5">{{ __('Catatan Tambahan (Optional)') }}</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-4 py-2 border border-[#e3e3e0] rounded-xl text-sm text-primaryText focus:ring-primary focus:border-primary transition" 
                                      placeholder="Tambahkan catatan khusus terkait jadwal ini...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                        <a href="{{ route('projects.timeline.show', $project->id) }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-xl text-sm font-semibold shadow-sm transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition">
                            {{ __('Simpan Jadwal') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toggle Milestone Name Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isMilestoneSelect = document.getElementById('is_milestone');
            const milestoneGroup = document.getElementById('milestone_name_group');

            if (isMilestoneSelect && milestoneGroup) {
                isMilestoneSelect.addEventListener('change', function () {
                    if (this.value === '1') {
                        milestoneGroup.classList.remove('hidden');
                    } else {
                        milestoneGroup.classList.add('hidden');
                        document.getElementById('milestone_name').value = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
