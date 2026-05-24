<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $categories = [
            'human_resource' => ['label' => 'Sumber Daya Manusia', 'color' => 'blue', 'icon' => 'fa-users', 'bg' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'infrastructure' => ['label' => 'Infrastruktur', 'color' => 'purple', 'icon' => 'fa-server', 'bg' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'tools' => ['label' => 'Software & Tools', 'color' => 'indigo', 'icon' => 'fa-laptop-code', 'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'operational' => ['label' => 'Operasional', 'color' => 'amber', 'icon' => 'fa-route', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'contingency' => ['label' => 'Biaya Cadangan', 'color' => 'rose', 'icon' => 'fa-shield-alt', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'other' => ['label' => 'Lain-lain', 'color' => 'gray', 'icon' => 'fa-box', 'bg' => 'bg-gray-50 text-gray-700 border-gray-200'],
        ];
    @endphp

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-cardSection rounded-xl p-6 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Kelola Budget Plan') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-gray-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>
                    @if($budgetItems->count() > 0)
                        <form action="{{ route('projects.budget.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi anggaran ini? Setelah finalized, seluruh rincian anggaran akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Anggaran') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
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

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: Budget Summary Column -->
                <div class="space-y-6">
                    <!-- Total Budget Card -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/15">
                        <span class="text-blue-100 text-xs font-semibold uppercase tracking-wider">{{ __('Total Anggaran RAB') }}</span>
                        <h3 class="text-3xl font-extrabold mt-1.5 leading-none">
                            Rp {{ number_format($budgetPlan->total_budget, 0, ',', '.') }}
                        </h3>
                        <p class="text-[10px] text-blue-200 mt-2 font-medium">
                            <i class="fas fa-calculator mr-1"></i>
                            {{ __('Dihitung otomatis dari total seluruh item anggaran') }}
                        </p>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-primaryText mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-primary"></i>
                            {{ __('Distribusi Anggaran') }}
                        </h4>
                        <div class="space-y-4">
                            @foreach($categories as $key => $cat)
                                @php
                                    $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                    $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                @endphp
                                <div class="group">
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                        <span class="text-secondaryText flex items-center gap-1.5">
                                            <i class="fas {{ $cat['icon'] }} w-4 text-center text-primary/70"></i>
                                            {{ $cat['label'] }}
                                        </span>
                                        <span class="text-primaryText font-mono">Rp {{ number_format($catSum, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full bg-{{ $cat['color'] }}-500 transition-all duration-500" style="width: {{ $percent }}%; background-color: currentColor; color: var(--color-{{ $cat['color'] }}-500, rgb(59, 130, 246));"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- General Settings / Notes -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-primaryText mb-3 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-primary"></i>
                            {{ __('Catatan Budget Plan') }}
                        </h4>
                        <form action="{{ route('projects.budget.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-primary focus:ring focus:ring-primary/20 mb-3" placeholder="Masukkan catatan umum... ">{{ old('notes', $budgetPlan->notes) }}</textarea>
                            <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                {{ __('Simpan Catatan') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Items Management Dashboard -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                        <!-- Section Title -->
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                            <div>
                                <h4 class="font-bold text-base text-primaryText">{{ __('Rincian Item RAB') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Kategori biaya dan rincian alokasi belanja proyek.') }}</p>
                            </div>
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition gap-1">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambah Item') }}
                            </button>
                        </div>

                        <!-- Budget Items List -->
                        @if($budgetItems->isEmpty())
                            <div class="p-12 text-center">
                                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Item Anggaran Kosong') }}</h5>
                                <p class="text-xs text-secondaryText mb-4">{{ __('Belum ada rincian alokasi dana belanja untuk proyek ini.') }}</p>
                                <button type="button" onclick="openAddModal()" class="inline-flex items-center px-3.5 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl text-xs font-semibold hover:bg-blue-600 hover:text-white transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambahkan Item Pertama') }}
                                </button>
                            </div>
                        @else
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle px-6">
                                    <table class="min-w-full text-left divide-y divide-gray-100">
                                        <thead>
                                            <tr class="text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                                <th class="py-3">{{ __('Kategori & Deskripsi') }}</th>
                                                <th class="py-3 text-center">{{ __('Qty') }}</th>
                                                <th class="py-3 text-right">{{ __('Biaya Satuan') }}</th>
                                                <th class="py-3 text-right">{{ __('Total Biaya') }}</th>
                                                <th class="py-3 text-right">{{ __('Aksi') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-xs">
                                            @foreach($budgetItems as $item)
                                                @php
                                                    $catConfig = $categories[$item->category] ?? $categories['other'];
                                                @endphp
                                                <tr class="hover:bg-gray-50/50 transition">
                                                    <td class="py-3.5 pr-3">
                                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $catConfig['bg'] }} mb-1">
                                                            <i class="fas {{ $catConfig['icon'] }} text-[8px]"></i>
                                                            {{ $catConfig['label'] }}
                                                        </div>
                                                        <div class="font-bold text-primaryText text-sm">{{ $item->description }}</div>
                                                        @if($item->notes)
                                                            <div class="text-[10px] text-gray-400 italic mt-0.5"><i class="far fa-comment mr-1"></i>{{ $item->notes }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-3.5 px-3 text-center font-semibold text-secondaryText">
                                                        {{ $item->quantity }} <span class="text-[10px] text-gray-400 font-normal ml-0.5">{{ $item->unit }}</span>
                                                    </td>
                                                    <td class="py-3.5 px-3 text-right font-mono text-secondaryText">
                                                        Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                                    </td>
                                                    <td class="py-3.5 px-3 text-right font-mono font-bold text-primaryText">
                                                        Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                                                    </td>
                                                    <td class="py-3.5 pl-3 text-right">
                                                        <div class="inline-flex gap-1.5">
                                                            <!-- Edit Button -->
                                                            <button type="button" 
                                                                    onclick='openEditModal({!! json_encode($item) !!})' 
                                                                    class="p-1.5 text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-600 hover:text-white transition"
                                                                    title="{{ __('Edit Item') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <!-- Delete Button -->
                                                            <form action="{{ route('projects.budget.items.delete', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item anggaran ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="p-1.5 text-rose-600 bg-rose-50 border border-rose-300 rounded-lg hover:bg-rose-600 hover:text-white transition"
                                                                        title="{{ __('Hapus Item') }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD BUDGET ITEM -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeAddModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.budget.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-plus text-primary"></i>
                                {{ __('Tambah Item Anggaran') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Kategori -->
                            <div>
                                <label for="add_category" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Kategori') }}</label>
                                <select name="category" id="add_category" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                    <option value="">-- {{ __('Pilih Kategori') }} --</option>
                                    @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="add_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Pekerjaan / Kebutuhan') }}</label>
                                <input type="text" name="description" id="add_description" required value="{{ old('description') }}" placeholder="Contoh: Honor Senior System Analyst, Lisensi Figma Professional" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Quantity & Unit (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="add_quantity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Quantity') }}</label>
                                    <input type="number" name="quantity" id="add_quantity" required min="1" value="{{ old('quantity', 1) }}" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="add_unit" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Satuan (Unit)') }}</label>
                                    <input type="text" name="unit" id="add_unit" required value="{{ old('unit', 'Bulan') }}" placeholder="Contoh: Orang, Unit, Paket" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Unit Cost -->
                            <div>
                                <label for="add_unit_cost" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Harga Satuan (Rp)') }}</label>
                                <input type="number" name="unit_cost" id="add_unit_cost" required min="0" value="{{ old('unit_cost') }}" placeholder="Contoh: 5000000" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Catatan Khusus (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Tuliskan keterangan tambahan untuk biaya ini jika ada... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            {{ __('Simpan Item') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT BUDGET ITEM -->
    <div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeEditModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Item Anggaran') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Kategori -->
                            <div>
                                <label for="edit_category" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Kategori') }}</label>
                                <select name="category" id="edit_category" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                    <option value="">-- {{ __('Pilih Kategori') }} --</option>
                                    @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}">{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="edit_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Pekerjaan / Kebutuhan') }}</label>
                                <input type="text" name="description" id="edit_description" required placeholder="Contoh: Honor Senior System Analyst" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Quantity & Unit (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_quantity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Quantity') }}</label>
                                    <input type="number" name="quantity" id="edit_quantity" required min="1" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                                <div>
                                    <label for="edit_unit" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Satuan (Unit)') }}</label>
                                    <input type="text" name="unit" id="edit_unit" required placeholder="Contoh: Orang" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Unit Cost -->
                            <div>
                                <label for="edit_unit_cost" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Harga Satuan (Rp)') }}</label>
                                <input type="number" name="edit_unit_cost" id="edit_unit_cost" required min="0" placeholder="Contoh: 5000000" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Catatan Khusus (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Keterangan tambahan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VANILLA JS MODALS TOGGLER -->
    <script>
        function openAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditModal(item) {
            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-item-form');
            
            // Set input values
            document.getElementById('edit_category').value = item.category;
            document.getElementById('edit_description').value = item.description;
            document.getElementById('edit_quantity').value = item.quantity;
            document.getElementById('edit_unit').value = item.unit;
            
            // Support database parsing representing decimals
            const costVal = parseFloat(item.unit_cost) || 0;
            document.getElementById('edit_unit_cost').value = Math.round(costVal);
            
            document.getElementById('edit_notes').value = item.notes || '';

            // Update form action dynamically
            form.action = `/projects/{{ $project->id }}/budget/items/${item.id}`;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</x-app-layout>
