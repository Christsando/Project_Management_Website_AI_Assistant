<x-app-layout>
    <x-slot name="header">
        <x-header-component :title="'Project Planning: WBS'" icon="fa-solid fa-sitemap text-blue-600 text-lg" />
    </x-slot>

    <div class="px-4 py-2">
        @php
            $userRole = strtolower(Auth::user()->role);
            $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
        @endphp

        <!-- Top Navigation Tab Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-200 pb-3 mb-6 gap-4">
            <div class="flex items-center gap-6">
                <span class="text-lg font-extrabold text-blue-600 tracking-tight">KelolaIN</span>
                <span class="text-slate-300">|</span>
                <a href="{{ route('projects.show', $project->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                    {{ __('Ringkasan') }}
                </a>
                <a href="{{ route('projects.wbs.show', $project->id) }}" class="text-xs font-bold text-blue-600 border-b-2 border-blue-600 pb-3.5 -mb-4 transition">
                    {{ __('Work Breakdown Structure') }}
                </a>
                <a href="{{ route('projects.timeline.show', $project->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                    {{ __('Timeline') }}
                </a>
            </div>
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                </span>
                <input type="text" id="wbsSearch" placeholder="Cari elemen WBS..." 
                       class="w-full pl-9 pr-4 py-1.5 bg-slate-100/60 border border-slate-200/50 rounded-full text-xs text-slate-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-400">
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center gap-2 shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-500 text-sm"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Blue Status Banner -->
        <div class="mb-6 p-6 rounded-3xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <!-- Decorative Drawing Blueprint Background -->
            <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 pointer-events-none hidden md:block">
                <svg class="w-full h-full text-white" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" stroke="currentColor" stroke-width="0.5">
                    <path d="M 0 10 L 100 90 M 0 90 L 100 10 M 10 0 L 10 100 M 90 0 L 90 100 M 0 50 L 100 50" />
                    <circle cx="50" cy="50" r="30" />
                    <rect x="25" y="25" width="50" height="50" />
                </svg>
            </div>

            <div class="space-y-4 max-w-2xl relative z-10">
                <h3 class="text-xl font-extrabold tracking-tight">{{ __('Perencanaan Struktur Proyek (WBS)') }}</h3>
                <p class="text-xs text-blue-50/90 leading-relaxed font-medium">
                    {{ __('Visualisasikan hierarki tugas dan deliverable proyek Anda secara terstruktur untuk memastikan setiap detail terkelola dengan baik.') }}
                </p>
                <div class="inline-flex items-center gap-2 py-1.5 px-3 bg-white/10 border border-white/10 rounded-full text-[10px] font-bold tracking-wide">
                    @if($isWbsFinalized)
                        <i class="fas fa-check-circle text-emerald-300"></i>
                        <span>{{ __('WBS Finalized - Siap untuk Timeline Planning') }}</span>
                    @else
                        <i class="fas fa-info-circle text-blue-200"></i>
                        <span>{{ __('Siap digunakan untuk Timeline Planning') }}</span>
                    @endif
                </div>
            </div>

            <div class="shrink-0 relative z-10">
                @if($isWbsFinalized)
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-blue-100 uppercase tracking-wider block">{{ __('Status Struktur') }}</span>
                            <span class="text-xs font-extrabold text-white block mt-0.5">{{ __('TELAH DIFINALISASI') }}</span>
                        </div>
                    </div>
                @else
                    @if($isPmo && $totalItems > 0)
                        <form action="{{ route('projects.wbs.finalize', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi WBS ini? Setelah finalized, seluruh item WBS akan dikunci dan tidak dapat diubah atau dihapus.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-slate-50 text-blue-700 font-extrabold rounded-2xl text-xs shadow-md transition duration-200 transform hover:-translate-y-0.5">
                                <i class="fas fa-check-double text-blue-600"></i>
                                {{ __('Finalisasi WBS') }}
                            </button>
                        </form>
                    @else
                        <div class="bg-white/10 border border-white/15 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/15 text-white rounded-xl flex items-center justify-center text-lg shadow-sm">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-blue-100 uppercase tracking-wider block">{{ __('Status Struktur') }}</span>
                                <span class="text-xs font-extrabold text-white block mt-0.5">{{ __('DRAF RENCANA') }}</span>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Scope Objective Card -->
        <div class="mb-6 bg-emerald-50/30 border border-emerald-100 p-5 rounded-2xl">
            <h4 class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i class="fas fa-sitemap"></i>
                {{ __('Referensi Tujuan Project Scope') }}
            </h4>
            <p class="text-xs text-slate-700 font-medium leading-relaxed">
                {{ $project->scope->objective }}
            </p>
        </div>

        <!-- Hierarchy Title & Toolbar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-extrabold text-slate-800 tracking-tight">
                    {{ __('Hierarki Proyek:') }} <span class="text-blue-600">{{ $project->title }}</span>
                </h3>
                <div class="flex items-center bg-slate-100 border border-slate-200 rounded-lg p-0.5">
                    <button class="p-1 text-slate-400 hover:text-slate-700 transition" title="Expand All" onclick="window.wbsToggleAll(true)">
                        <i class="fas fa-angle-double-down text-xs"></i>
                    </button>
                    <button class="p-1 text-slate-400 hover:text-slate-700 transition" title="Collapse All" onclick="window.wbsToggleAll(false)">
                        <i class="fas fa-angle-double-up text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-800 font-bold rounded-xl text-xs transition shadow-sm gap-2" onclick="window.wbsResetFilter()">
                    <i class="fas fa-filter text-[10px]"></i>
                    {{ __('Filter') }}
                </button>
                @if($isPmo && !$isWbsFinalized)
                    <a href="{{ route('projects.wbs.create', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                        <i class="fas fa-plus text-[10px]"></i>
                        {{ __('Tambah Item') }}
                    </a>
                @endif
            </div>
        </div>

        <!-- WBS Hierarchy Tree Cards -->
        <div class="space-y-6 mb-8" id="wbsTreeContainer">
            @if($wbsItems->isEmpty())
                <div class="p-12 bg-white border border-slate-100 rounded-3xl text-center shadow-sm">
                    <div class="w-16 h-16 bg-slate-50 border border-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-list-ol text-2xl"></i>
                    </div>
                    <h4 class="font-extrabold text-lg text-slate-800 mb-1">{{ __('Belum ada item WBS') }}</h4>
                    <p class="text-xs text-slate-500 mb-4">{{ __('Struktur kerja WBS belum dibuat untuk proyek ini.') }}</p>
                    @if($isPmo)
                        <a href="{{ route('projects.wbs.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition gap-2">
                            <i class="fas fa-plus text-[10px]"></i>
                            {{ __('Tambah Item WBS Pertama') }}
                        </a>
                    @endif
                </div>
            @else
                <!-- Loop root items (Level 1) -->
                @foreach($wbsItems as $item)
                    @php
                        $priorityColors = [
                            'low' => 'bg-slate-100 text-slate-600 border-slate-200/60',
                            'medium' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'high' => 'bg-rose-50 text-rose-700 border-rose-100',
                        ][$item->priority] ?? 'bg-slate-100 text-slate-600 border-slate-200/60';
                    @endphp
                    <div class="wbs-level-container" data-searchable-item data-item-id="{{ $item->id }}" data-search-text="{{ $item->title }} {{ $item->description }} {{ $item->deliverable }}">
                        <!-- Level 1 Card -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between p-5 bg-white border border-slate-200/80 rounded-2xl shadow-sm hover:shadow-md transition duration-200 gap-4 relative z-10">
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <!-- Icon phase -->
                                <div class="w-12 h-12 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                    <i class="fas fa-cubes text-base"></i>
                                </div>
                                <div class="space-y-1 min-w-0">
                                    <h4 class="font-extrabold text-slate-800 text-sm md:text-base leading-tight">{{ $item->title }}</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed max-w-2xl">{{ $item->description }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex items-center gap-6 lg:gap-8 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                                <!-- Deliverable -->
                                <div class="lg:w-48">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Deliverable</span>
                                    <span class="text-xs font-bold text-slate-700 block truncate max-w-[12rem]" title="{{ $item->deliverable ?: '-' }}">
                                        {{ $item->deliverable ?: '-' }}
                                    </span>
                                </div>
                                <!-- Priority -->
                                <div class="lg:w-24">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Prioritas</span>
                                    <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-[10px] font-bold border {{ $priorityColors }}">
                                        {{ ucfirst($item->priority) }}
                                    </span>
                                </div>
                                <!-- Duration -->
                                <div class="lg:w-20">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Durasi</span>
                                    <span class="text-xs font-extrabold text-slate-700 block">
                                        {{ $item->estimated_duration_days ? $item->estimated_duration_days . ' Hari' : '-' }}
                                    </span>
                                </div>
                                <!-- Actions -->
                                <div class="col-span-2 sm:col-span-1 lg:w-24 flex justify-end">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($isPmo && !$isWbsFinalized)
                                            <!-- Add child shortcut -->
                                            <a href="{{ route('projects.wbs.create', ['project' => $project->id, 'parent_id' => $item->id]) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm" 
                                               title="{{ __('Tambah Sub-tugas') }}">
                                                <i class="fas fa-plus text-xs"></i>
                                            </a>
                                            <a href="{{ route('projects.wbs.edit', [$project->id, $item->id]) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-sm" 
                                               title="{{ __('Ubah') }}">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            @if($item->status === 'draft')
                                                <form action="{{ route('projects.wbs.destroy', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item WBS ini? Menghapus item ini akan ikut menghapus seluruh sub-task di bawahnya.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center justify-center w-8 h-8 text-rose-600 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-600 hover:text-white transition shadow-sm" 
                                                            title="{{ __('Hapus') }}">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-[10px] text-slate-400 italic font-semibold">{{ __('Kunci') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Children level 2 -->
                        @if($item->children->isNotEmpty())
                            <div class="relative pl-12 space-y-6 pt-4 wbs-children-wrapper">
                                @foreach($item->children as $child)
                                    @php
                                        $childPriorityColors = [
                                            'low' => 'bg-slate-100 text-slate-600 border-slate-200/60',
                                            'medium' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'high' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        ][$child->priority] ?? 'bg-slate-100 text-slate-600 border-slate-200/60';
                                        
                                        $isLastChild = $loop->last;
                                        $hasL3 = $child->children->isNotEmpty();
                                    @endphp
                                    <div class="relative wbs-level-container" data-searchable-item data-item-id="{{ $child->id }}" data-parent-id="{{ $item->id }}" data-search-text="{{ $child->title }} {{ $child->description }} {{ $child->deliverable }}">
                                        <!-- Vertical connector line -->
                                        <div class="absolute left-[-1.5rem] top-0 {{ $isLastChild && !$hasL3 ? 'h-[2.5rem]' : 'bottom-0' }} w-0.5 bg-slate-200"></div>
                                        <!-- Horizontal connector bend line -->
                                        <div class="absolute left-[-1.5rem] top-[2.5rem] w-6 h-0.5 bg-slate-200"></div>

                                        <!-- Level 2 Card -->
                                        <div class="flex flex-col lg:flex-row lg:items-center justify-between p-4.5 bg-white border border-slate-200/70 rounded-xl hover:border-slate-300 transition duration-150 gap-4 shadow-sm relative z-10">
                                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                                <!-- bullet/marker -->
                                                <div class="w-8 h-8 bg-slate-50 border border-slate-100 text-slate-400 rounded-lg flex items-center justify-center shrink-0">
                                                    <i class="fas fa-code-branch text-xs"></i>
                                                </div>
                                                <div class="space-y-0.5 min-w-0">
                                                    <h5 class="font-extrabold text-slate-800 text-xs md:text-sm leading-tight">{{ $child->title }}</h5>
                                                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-xl">{{ $child->description }}</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex items-center gap-6 lg:gap-8 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                                                <!-- Deliverable -->
                                                <div class="lg:w-48">
                                                    <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Deliverable</span>
                                                    <span class="text-xs font-bold text-slate-700 block truncate max-w-[12rem]" title="{{ $child->deliverable ?: '-' }}">
                                                        {{ $child->deliverable ?: '-' }}
                                                    </span>
                                                </div>
                                                <!-- Priority -->
                                                <div class="lg:w-24">
                                                    <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Prioritas</span>
                                                    <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[9px] font-bold border {{ $childPriorityColors }}">
                                                        {{ ucfirst($child->priority) }}
                                                    </span>
                                                </div>
                                                <!-- Duration -->
                                                <div class="lg:w-20">
                                                    <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Durasi</span>
                                                    <span class="text-xs font-extrabold text-slate-700 block">
                                                        {{ $child->estimated_duration_days ? $child->estimated_duration_days . ' Hari' : '-' }}
                                                    </span>
                                                </div>
                                                <!-- Actions -->
                                                <div class="col-span-2 sm:col-span-1 lg:w-24 flex justify-end">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        @if($isPmo && !$isWbsFinalized)
                                                            <!-- Add child shortcut -->
                                                            <a href="{{ route('projects.wbs.create', ['project' => $project->id, 'parent_id' => $child->id]) }}" 
                                                               class="inline-flex items-center justify-center w-7 h-7 text-blue-600 bg-blue-50 border border-blue-100 rounded-md hover:bg-blue-600 hover:text-white transition shadow-sm" 
                                                               title="{{ __('Tambah Sub-tugas') }}">
                                                                <i class="fas fa-plus text-[10px]"></i>
                                                            </a>
                                                            <a href="{{ route('projects.wbs.edit', [$project->id, $child->id]) }}" 
                                                               class="inline-flex items-center justify-center w-7 h-7 text-amber-700 bg-amber-50 border border-amber-200 rounded-md hover:bg-amber-600 hover:text-white transition shadow-sm" 
                                                               title="{{ __('Ubah') }}">
                                                                <i class="fas fa-edit text-[10px]"></i>
                                                            </a>
                                                            @if($child->status === 'draft')
                                                                <form action="{{ route('projects.wbs.destroy', [$project->id, $child->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item WBS ini? Menghapus item ini akan ikut menghapus seluruh sub-task di bawahnya.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="inline-flex items-center justify-center w-7 h-7 text-rose-600 bg-rose-50 border border-rose-200 rounded-md hover:bg-rose-600 hover:text-white transition shadow-sm" 
                                                                            title="{{ __('Hapus') }}">
                                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @else
                                                            <span class="text-[9px] text-slate-400 italic font-semibold">{{ __('Kunci') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Children level 3 -->
                                        @if($hasL3)
                                            <div class="relative ml-8 mt-3 p-4 bg-slate-50/50 border border-dashed border-slate-200/80 rounded-2xl space-y-3 relative z-10 wbs-level-3-box">
                                                <!-- Left tree line indicator inside the box -->
                                                <div class="absolute left-[-1.5rem] top-0 bottom-4 w-0.5 bg-slate-200"></div>

                                                @foreach($child->children as $grandchild)
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs py-1.5 hover:bg-slate-100/40 rounded-lg px-2 transition group/l3 relative wbs-level-container" 
                                                         data-searchable-item data-item-id="{{ $grandchild->id }}" data-parent-id="{{ $child->id }}" data-search-text="{{ $grandchild->title }} {{ $grandchild->description }} {{ $grandchild->deliverable }}">
                                                        
                                                        <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0 mt-1.5"></span>
                                                            <div class="min-w-0">
                                                                <span class="font-extrabold text-slate-800 text-xs">{{ $grandchild->title }}</span>
                                                                @if($grandchild->deliverable)
                                                                    <span class="text-[10px] text-slate-400 font-bold ml-1.5">({{ __('Deliverable: ') }}{{ $grandchild->deliverable }})</span>
                                                                @endif
                                                                @if($grandchild->description)
                                                                    <p class="text-[10px] text-slate-500 font-normal mt-0.5 leading-relaxed">{{ $grandchild->description }}</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center justify-end gap-3 mt-2 sm:mt-0 shrink-0 pl-4">
                                                            <!-- Priority tag if any -->
                                                            @if($grandchild->priority !== 'medium')
                                                                <span class="text-[8px] font-bold uppercase tracking-wider {{ $grandchild->priority === 'high' ? 'text-rose-500' : 'text-slate-400' }}">
                                                                    {{ $grandchild->priority }}
                                                                </span>
                                                            @endif
                                                            
                                                            <!-- Duration Pill -->
                                                            @if($grandchild->estimated_duration_days)
                                                                <span class="bg-indigo-50 text-indigo-700 text-[10px] font-extrabold px-2 py-0.5 rounded-md border border-indigo-100">
                                                                    {{ $grandchild->estimated_duration_days }} Hari
                                                                </span>
                                                            @endif

                                                            <!-- Inline Actions -->
                                                            @if($isPmo && !$isWbsFinalized)
                                                                <div class="opacity-0 group-hover/l3:opacity-100 focus-within:opacity-100 transition duration-150 inline-flex items-center gap-1">
                                                                    <a href="{{ route('projects.wbs.edit', [$project->id, $grandchild->id]) }}" 
                                                                       class="text-amber-600 hover:text-amber-800 p-1 transition" 
                                                                       title="{{ __('Ubah') }}">
                                                                        <i class="fas fa-edit text-[10px]"></i>
                                                                    </a>
                                                                    @if($grandchild->status === 'draft')
                                                                        <form action="{{ route('projects.wbs.destroy', [$project->id, $grandchild->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item WBS ini?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" 
                                                                                    class="text-rose-600 hover:text-rose-800 p-1 transition" 
                                                                                    title="{{ __('Hapus') }}">
                                                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Add Root WBS Item Button -->
                @if($isPmo && !$isWbsFinalized)
                    <div class="mt-8">
                        <a href="{{ route('projects.wbs.create', $project->id) }}" class="flex flex-col items-center justify-center py-6 bg-slate-50/50 hover:bg-slate-100/80 border-2 border-dashed border-slate-200 hover:border-blue-400/50 rounded-2xl transition duration-200 group text-center cursor-pointer">
                            <div class="w-10 h-10 bg-white shadow-sm border border-slate-100 rounded-full flex items-center justify-center text-slate-500 group-hover:text-blue-600 transition mb-2">
                                <i class="fas fa-plus text-sm"></i>
                            </div>
                            <span class="text-xs font-extrabold text-slate-500 group-hover:text-slate-700 transition uppercase tracking-wider">{{ __('Tambah Elemen WBS Utama') }}</span>
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Frontend Interactive Tree Helper Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Frontend real-time search filtering
            const searchInput = document.getElementById('wbsSearch');
            searchInput?.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                const items = document.querySelectorAll('[data-searchable-item]');
                
                if (!query) {
                    // Show everything and restore height checks
                    items.forEach(el => {
                        el.style.display = '';
                        el.classList.remove('opacity-40');
                        // Restore connector line height
                        const line = el.querySelector(':scope > div.absolute.left-[-1.5rem].top-0');
                        if (line) line.style.display = '';
                    });
                    return;
                }
                
                // Hide all searchable units
                items.forEach(el => {
                    el.style.display = 'none';
                    el.classList.remove('opacity-40');
                });
                
                // Track matches
                items.forEach(el => {
                    const searchText = el.getAttribute('data-search-text').toLowerCase();
                    if (searchText.includes(query)) {
                        // Display match
                        el.style.display = '';
                        
                        // Show all ancestors of this element
                        let parentId = el.getAttribute('data-parent-id');
                        while (parentId) {
                            const parentEl = document.querySelector(`[data-item-id="${parentId}"]`);
                            if (parentEl) {
                                parentEl.style.display = '';
                                // Highlight parent slightly differently to show it's context
                                parentEl.classList.add('opacity-40');
                                parentId = parentEl.getAttribute('data-parent-id');
                            } else {
                                parentId = null;
                            }
                        }

                        // Also show children if this matched phase has sub-elements
                        const itemId = el.getAttribute('data-item-id');
                        document.querySelectorAll(`[data-parent-id="${itemId}"]`).forEach(child => {
                            child.style.display = '';
                        });
                    }
                });

                // Hide connector lines on search results to avoid visuals hanging in space
                items.forEach(el => {
                    const line = el.querySelector(':scope > div.absolute.left-[-1.5rem].top-0');
                    if (line) {
                        line.style.display = 'none';
                    }
                });
            });

            // Expand/Collapse Helpers
            window.wbsToggleAll = function(expand) {
                const containers = document.querySelectorAll('.wbs-children-wrapper, .wbs-level-3-box');
                containers.forEach(container => {
                    if (expand) {
                        container.style.display = '';
                    } else {
                        container.style.display = 'none';
                    }
                });
            };

            // Reset Filter
            window.wbsResetFilter = function() {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
                window.wbsToggleAll(true);
            };
        });
    </script>
</x-app-layout>
