<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @php
        $categories = [
            'human_resource' => ['label' => 'SDM', 'color' => 'blue', 'icon' => 'fa-users', 'bg' => 'bg-[#E0F2FE] text-[#0284C7] border-[#BAE6FD]', 'hex' => '#0284c7'],
            'infrastructure' => ['label' => 'INFRASTRUKTUR', 'color' => 'purple', 'icon' => 'fa-server', 'bg' => 'bg-[#F3E8FF] text-[#7E22CE] border-[#E9D5FF]', 'hex' => '#7e22ce'],
            'tools' => ['label' => 'ALAT', 'color' => 'green', 'icon' => 'fa-laptop-code', 'bg' => 'bg-[#DCFCE7] text-[#15803D] border-[#BBF7D0]', 'hex' => '#15803d'],
            'operational' => ['label' => 'OPERASIONAL', 'color' => 'rose', 'icon' => 'fa-route', 'bg' => 'bg-[#FFE4E6] text-[#E11D48] border-[#FECDD3]', 'hex' => '#e11d48'],
            'contingency' => ['label' => 'CADANGAN', 'color' => 'amber', 'icon' => 'fa-shield-alt', 'bg' => 'bg-[#FEF3C7] text-[#D97706] border-[#FDE68A]', 'hex' => '#d97706'],
            'other' => ['label' => 'LAIN-LAIN', 'color' => 'gray', 'icon' => 'fa-box', 'bg' => 'bg-[#F3F4F6] text-[#4B5563] border-[#E5E7EB]', 'hex' => '#4b5563'],
        ];
    @endphp

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-6xl mx-auto">
            <!-- Back Navigation -->
            <div class="mb-4">
                <a href="{{ route('project-planning.budget.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-slate-600 transition gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-slate-50 pb-5">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ __('Perencanaan Anggaran (RAB)') }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Kelola alokasi dana proyek secara presisi dan transparan.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-400 font-medium">
                        <span>{{ __('Proyek:') }}</span>
                        <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $project->title }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-slate-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>
                    
                    <a href="{{ route('projects.budget.edit', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-sm transition gap-1.5">
                        <i class="fas fa-redo text-slate-400"></i>
                        {{ __('Inisialisasi RAB') }}
                    </a>

                    @if($budgetItems->count() > 0)
                        <form action="{{ route('projects.budget.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi anggaran ini? Setelah finalized, seluruh rincian anggaran akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Anggaran') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs flex items-center gap-2.5 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold text-sm">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>{{ __('Terdapat kesalahan input:') }}</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 font-semibold text-slate-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Banner status -->
            <div class="mb-6 p-5 rounded-2xl bg-blue-600 text-white shadow-sm flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">{{ __('Siap digunakan untuk Human Resource Planning') }}</h4>
                        <p class="text-xs text-blue-100 mt-1 leading-relaxed font-semibold">
                            {{ __('Sistem kini terintegrasi dengan modul SDM untuk estimasi biaya tenaga kerja otomatis berdasarkan role dan durasi proyek.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: Budget Summary Column -->
                <div class="space-y-6">
                    <!-- Total Budget Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between min-h-[140px] relative overflow-hidden">
                        <div>
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block flex items-center gap-1.5">
                                <i class="fa-solid fa-wallet text-blue-600"></i>
                                {{ __('Total Anggaran Proyek') }}
                            </span>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-3 tracking-tight">
                                Rp {{ number_format($budgetPlan->total_budget, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#DCFCE7] text-[#15803D]">
                                <i class="fa-solid fa-check text-[9px]"></i>
                                {{ __('Sesuai dengan pagu anggaran') }}
                            </span>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-slate-800 mb-5 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-blue-600"></i>
                            {{ __('Distribusi Kategori') }}
                        </h4>

                        @php
                            $totalBudget = $budgetPlan->total_budget ?: 1;
                            $accumulatedPercent = 0;
                            $circumference = 251.2; // 2 * pi * 40
                            $svgCircles = [];
                            
                            foreach($categories as $key => $cat) {
                                $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                
                                if ($percent > 0) {
                                    $dashArray = ($percent / 100) * $circumference;
                                    $dashOffset = -($accumulatedPercent / 100) * $circumference;
                                    $accumulatedPercent += $percent;
                                    
                                    $svgCircles[] = [
                                        'dash' => "$dashArray $circumference",
                                        'offset' => $dashOffset,
                                        'color' => $cat['hex'],
                                        'label' => $cat['label'],
                                        'percent' => round($percent)
                                    ];
                                }
                            }
                        @endphp

                        <div class="relative flex items-center justify-center mb-6">
                            <div class="w-36 h-36">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <!-- Background Circle -->
                                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f1f5f9" stroke-width="10" />
                                    @if(empty($svgCircles))
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#cbd5e1" stroke-width="10" />
                                    @else
                                        @foreach($svgCircles as $circle)
                                            <circle cx="50" cy="50" r="40" fill="transparent" 
                                                    stroke="{{ $circle['color'] }}" 
                                                    stroke-width="10" 
                                                    stroke-dasharray="{{ $circle['dash'] }}" 
                                                    stroke-dashoffset="{{ $circle['offset'] }}"
                                                    class="transition-all duration-300 hover:stroke-[12px] cursor-pointer" />
                                        @endforeach
                                    @endif
                                </svg>
                            </div>
                            <div class="absolute flex flex-col items-center justify-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ __('Total') }}</span>
                                <span class="text-base font-extrabold text-slate-800">100%</span>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="space-y-3.5 mt-4">
                            @foreach($categories as $key => $cat)
                                @php
                                    $catSum = $budgetItems->where('category', $key)->sum('total_cost');
                                    $percent = $budgetPlan->total_budget > 0 ? ($catSum / $budgetPlan->total_budget) * 100 : 0;
                                @endphp
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $cat['hex'] }}"></span>
                                        {{ $cat['label'] }}
                                    </span>
                                    <span class="text-slate-800 font-mono">{{ round($percent) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- KelolaIN Academy Card -->
                    <div class="relative rounded-2xl overflow-hidden h-[130px] shadow-sm group">
                        <img src="/images/kelolain_academy.png" alt="KelolaIN Academy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/50 to-transparent flex flex-col justify-end p-4">
                            <p class="text-xs font-bold text-white leading-snug">
                                {{ __('Pelajari panduan efisiensi anggaran korporat di KelolaIN Academy →') }}
                            </p>
                        </div>
                    </div>

                    <!-- General Settings / Notes -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-slate-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-blue-600"></i>
                            {{ __('Catatan Budget Plan') }}
                        </h4>
                        <form action="{{ route('projects.budget.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <textarea name="notes" rows="4" class="w-full rounded-xl border-slate-200 text-xs shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 mb-3" placeholder="Masukkan catatan umum... ">{{ old('notes', $budgetPlan->notes) }}</textarea>
                            <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                {{ __('Simpan Catatan') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Items Management Dashboard -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <!-- Section Title -->
                        <div class="flex items-center justify-between border-b border-slate-50 pb-4 mb-4">
                            <div>
                                <h4 class="font-bold text-base text-slate-800">{{ __('Rincian Item Anggaran') }}</h4>
                            </div>
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition gap-1.5">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambah Item') }}
                            </button>
                        </div>

                        <!-- Budget Items List -->
                        @if($budgetItems->isEmpty())
                            <div class="p-16 text-center">
                                <div class="w-12 h-12 bg-slate-50 text-slate-400 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <h5 class="font-bold text-sm text-slate-800 mb-1">{{ __('Item Anggaran Kosong') }}</h5>
                                <p class="text-xs text-slate-500 mb-4">{{ __('Belum ada rincian alokasi dana belanja untuk proyek ini.') }}</p>
                                <button type="button" onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambahkan Item Pertama') }}
                                </button>
                            </div>
                        @else
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle px-6">
                                    <table class="min-w-full text-left divide-y divide-slate-50">
                                        <thead>
                                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                <th class="py-3">{{ __('KATEGORI') }}</th>
                                                <th class="py-3 px-3">{{ __('DESKRIPSI') }}</th>
                                                <th class="py-3 text-center">{{ __('QTY') }}</th>
                                                <th class="py-3 text-center">{{ __('SATUAN') }}</th>
                                                <th class="py-3 text-right">{{ __('HARGA SATUAN') }}</th>
                                                <th class="py-3 text-right">{{ __('AKSI') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 text-xs">
                                            @foreach($budgetItems as $item)
                                                @php
                                                    $catConfig = $categories[$item->category] ?? $categories['other'];
                                                @endphp
                                                <tr class="hover:bg-slate-50/30 transition duration-150">
                                                    <td class="py-4 pr-3">
                                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[9px] font-bold border {{ $catConfig['bg'] }}">
                                                            {{ $catConfig['label'] }}
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-3">
                                                        <div class="font-bold text-slate-800 text-sm">{{ $item->description }}</div>
                                                        @if($item->notes)
                                                            <div class="text-[10px] text-slate-400 italic mt-1.5 flex items-center gap-1">
                                                                <i class="far fa-comment"></i>
                                                                {{ $item->notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="py-4 text-center font-bold text-slate-700">
                                                        {{ $item->quantity }}
                                                    </td>
                                                    <td class="py-4 text-center font-semibold text-slate-400">
                                                        {{ $item->unit }}
                                                    </td>
                                                    <td class="py-4 text-right">
                                                        <div class="font-bold text-slate-800 text-sm">
                                                            Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                                            {{ __('Total: ') }}Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td class="py-4 pl-3 text-right">
                                                        <div class="inline-flex gap-1.5 justify-end">
                                                            <!-- Edit Button -->
                                                            <button type="button" 
                                                                    onclick='openEditModal({!! json_encode($item) !!})' 
                                                                    class="p-1.5 text-amber-600 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-sm"
                                                                    title="{{ __('Edit Item') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <!-- Delete Button -->
                                                            <form action="{{ route('projects.budget.items.delete', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item anggaran ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="p-1.5 text-rose-600 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-600 hover:text-white transition shadow-sm"
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

                    <!-- SDM Active Integration Alert Banner -->
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-600/10 text-blue-600 rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-800">{{ __('Integrasi SDM Aktif') }}</h5>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                {{ __('Budget ini telah terhubung dengan daftar personil proyek. Setiap perubahan pada durasi kontrak di modul SDM akan secara otomatis memperbarui nominal anggaran SDM di atas.') }}
                            </p>
                            <a href="{{ route('projects.human-resource.show', $project->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 mt-3 transition">
                                {{ __('Lihat Detail Personil Proyek') }}
                                <i class="fas fa-external-link-alt text-[9px]"></i>
                            </a>
                        </div>
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
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.budget.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-plus text-blue-600"></i>
                                {{ __('Tambah Item Anggaran') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Kategori -->
                            <div>
                                <label for="add_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Kategori') }}</label>
                                <select name="category" id="add_category" required class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">-- {{ __('Pilih Kategori') }} --</option>
                                    @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="add_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Kebutuhan') }}</label>
                                <input type="text" name="description" id="add_description" required value="{{ old('description') }}" placeholder="Contoh: Honor Senior System Analyst, Lisensi Figma Professional" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Quantity & Unit (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="add_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Quantity') }}</label>
                                    <input type="number" name="quantity" id="add_quantity" required min="1" value="{{ old('quantity', 1) }}" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label for="add_unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Satuan (Unit)') }}</label>
                                    <input type="text" name="unit" id="add_unit" required value="{{ old('unit', 'Bulan') }}" placeholder="Contoh: Orang, Unit, Paket" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>

                            <!-- Unit Cost -->
                            <div>
                                <label for="add_unit_cost" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Harga Satuan (Rp)') }}</label>
                                <input type="number" name="unit_cost" id="add_unit_cost" required min="0" value="{{ old('unit_cost') }}" placeholder="Contoh: 5000000" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Catatan Khusus (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Tuliskan keterangan tambahan untuk biaya ini jika ada... ">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <!-- Center Align -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Item Anggaran') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Kategori -->
                            <div>
                                <label for="edit_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Kategori') }}</label>
                                <select name="category" id="edit_category" required class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">-- {{ __('Pilih Kategori') }} --</option>
                                    @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}">{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="edit_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Deskripsi Pekerjaan / Kebutuhan') }}</label>
                                <input type="text" name="description" id="edit_description" required placeholder="Contoh: Honor Senior System Analyst" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Quantity & Unit (Grid) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Quantity') }}</label>
                                    <input type="number" name="quantity" id="edit_quantity" required min="1" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label for="edit_unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Satuan (Unit)') }}</label>
                                    <input type="text" name="unit" id="edit_unit" required placeholder="Contoh: Orang" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>

                            <!-- Unit Cost -->
                            <div>
                                <label for="edit_unit_cost" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Harga Satuan (Rp)') }}</label>
                                <input type="number" name="unit_cost" id="edit_unit_cost" required min="0" placeholder="Contoh: 5000000" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Catatan Khusus (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Keterangan tambahan... "></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
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
