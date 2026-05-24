<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4 pb-12">
        <div class="bg-cardSection rounded-xl p-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.risk-management.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Kelola Risk Management Plan') }}
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
                    @if($riskItems->count() > 0)
                        <form action="{{ route('projects.risk-management.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi rencana manajemen risiko ini? Setelah finalized, seluruh alokasi risiko dan rencana mitigasi akan dikunci dan tidak dapat diubah lagi.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                <i class="fas fa-check-double"></i>
                                {{ __('Finalisasi Rencana Risiko') }}
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

            <!-- Main Layout Flex Container (Content Left, AI Right) -->
            <div class="flex flex-col lg:flex-row gap-6 w-full">
                
                <!-- Left: Content Section (Stats & Risk Items) -->
                <div class="flex-1 min-w-0 space-y-6">
                    
                    <!-- Stats Aggregates Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Total Risiko -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-md shadow-blue-500/15 transition-all hover:scale-[1.02] duration-300">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-blue-100 text-[10px] font-bold uppercase tracking-wider block">{{ __('Total Risiko') }}</span>
                                    <span class="text-3xl font-extrabold block mt-2">{{ $totalRisks }}</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Probabilitas Tinggi -->
                        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm transition-all hover:scale-[1.02] duration-300 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider block">{{ __('Probabilitas Tinggi') }}</span>
                                    <span class="text-3xl font-extrabold text-rose-600 block mt-2">{{ $probHigh }}</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Keparahan Tinggi -->
                        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm transition-all hover:scale-[1.02] duration-300 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider block">{{ __('Keparahan Tinggi') }}</span>
                                    <span class="text-3xl font-extrabold text-orange-600 block mt-2">{{ $sevHigh }}</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500">
                                    <i class="fas fa-biohazard"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Status Open -->
                        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm transition-all hover:scale-[1.02] duration-300 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider block">{{ __('Status Open') }}</span>
                                    <span class="text-3xl font-extrabold text-blue-600 block mt-2">{{ $statusOpen }}</span>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Items Management Panel -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm p-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                            <div>
                                <h4 class="font-bold text-base text-primaryText">{{ __('Daftar Item Potensi Risiko') }}</h4>
                                <p class="text-xs text-secondaryText mt-0.5">{{ __('Pemetaan risiko, probability, severity, rencana mitigasi, & PIC.') }}</p>
                            </div>
                            <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-1.5">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambah Risiko') }}
                            </button>
                        </div>

                        <!-- Table -->
                        @if($riskItems->isEmpty())
                            <div class="p-12 text-center">
                                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <h5 class="font-bold text-sm text-primaryText mb-1">{{ __('Belum Ada Risiko Terdaftar') }}</h5>
                                <p class="text-xs text-secondaryText mb-4">{{ __('Rencana risiko Anda kosong. Tambahkan item risiko secara manual atau gunakan AI Assistant di panel kanan.') }}</p>
                                <button type="button" onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl text-xs font-semibold hover:bg-blue-600 hover:text-white transition gap-1.5">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Tambah Item Risiko') }}
                                </button>
                            </div>
                        @else
                            <div class="overflow-x-auto -mx-6">
                                <div class="inline-block min-w-full align-middle px-6">
                                    <table class="min-w-full text-left divide-y divide-gray-100 table-fixed">
                                        <thead>
                                            <tr class="text-xs font-bold text-secondaryText uppercase tracking-wider">
                                                <th class="py-3.5 pr-4 w-1/3 min-w-[240px]">{{ __('Risiko & Penyebab') }}</th>
                                                <th class="py-3.5 px-4 w-[130px]">{{ __('Parameter') }}</th>
                                                <th class="py-3.5 px-4 w-1/3 min-w-[240px]">{{ __('Penanganan (Mitigasi/Kontingensi)') }}</th>
                                                <th class="py-3.5 px-4 w-[160px] text-center">{{ __('WBS / Owner') }}</th>
                                                <th class="py-3.5 pl-4 w-[100px] text-right">{{ __('Aksi') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-xs">
                                            @foreach($riskItems as $item)
                                                <tr class="hover:bg-gray-50/50 transition">
                                                    <td class="py-4 pr-4 align-top">
                                                        <div class="font-bold text-primaryText text-sm leading-tight">{{ $item->risk_title }}</div>
                                                        <div class="text-[11px] text-gray-500 mt-1.5 leading-relaxed" title="{{ $item->risk_description }}">{{ $item->risk_description }}</div>
                                                        @if($item->risk_cause)
                                                            <div class="text-[10px] text-secondaryText mt-2 bg-gray-50 p-2 rounded-lg border border-gray-100/50">
                                                                <span class="font-bold text-[9px] uppercase tracking-wider text-gray-400 block mb-0.5">Penyebab:</span>
                                                                {{ $item->risk_cause }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="py-4 px-4 align-top">
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-[9px] uppercase font-bold text-gray-400 w-8">Prob:</span>
                                                                @if($item->probability === 'high')
                                                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md text-[9px] font-bold">High</span>
                                                                @elseif($item->probability === 'medium')
                                                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md text-[9px] font-bold">Medium</span>
                                                                @else
                                                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[9px] font-bold">Low</span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-[9px] uppercase font-bold text-gray-400 w-8">Sev:</span>
                                                                @if($item->severity === 'high')
                                                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md text-[9px] font-bold">High</span>
                                                                @elseif($item->severity === 'medium')
                                                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md text-[9px] font-bold">Medium</span>
                                                                @else
                                                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[9px] font-bold">Low</span>
                                                                @endif
                                                            </div>
                                                            <div class="pt-1.5">
                                                                @if($item->status === 'open')
                                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Open</span>
                                                                @elseif($item->status === 'mitigated')
                                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Mitigated</span>
                                                                @elseif($item->status === 'accepted')
                                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Accepted</span>
                                                                @else
                                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-700 border border-gray-200">Closed</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-4 px-4 align-top space-y-2">
                                                        <div class="text-[11px] leading-relaxed bg-emerald-50/30 p-2.5 rounded-lg border border-emerald-100/50">
                                                            <span class="font-bold text-[9px] text-emerald-800 uppercase tracking-wider block mb-1">Mitigasi (Preventif):</span>
                                                            {{ $item->mitigation_plan }}
                                                        </div>
                                                        @if($item->contingency_plan)
                                                            <div class="text-[11px] leading-relaxed bg-blue-50/30 p-2.5 rounded-lg border border-blue-100/50">
                                                                <span class="font-bold text-[9px] text-blue-800 uppercase tracking-wider block mb-1">Kontingensi (Reaktif):</span>
                                                                {{ $item->contingency_plan }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="py-4 px-4 align-top text-center">
                                                        @if($item->wbsItem)
                                                            <div class="font-bold text-primaryText text-xs truncate max-w-[140px] mx-auto bg-gray-50 border border-gray-100 rounded-lg p-1.5" title="{{ $item->wbsItem->title }}">
                                                                {{ $item->wbsItem->title }}
                                                            </div>
                                                            <div class="text-[9px] text-gray-400 font-mono mt-1">WBS ID: #{{ $item->related_wbs_item_id }}</div>
                                                        @else
                                                            <span class="text-gray-400 italic text-[10px] block">-</span>
                                                        @endif
                                                        <div class="text-[10px] text-secondaryText font-bold mt-2">
                                                            <i class="fas fa-user-circle mr-1 text-gray-400"></i>{{ $item->risk_owner ?: __('Tanpa Owner') }}
                                                        </div>
                                                    </td>
                                                    <td class="py-4 pl-4 align-top text-right">
                                                        <div class="inline-flex gap-1.5">
                                                            <!-- Edit Button -->
                                                            <button type="button" 
                                                                    data-item="{{ json_encode($item) }}"
                                                                    onclick="openEditModalFromBtn(this)" 
                                                                    class="p-2 text-amber-700 bg-amber-50 border border-amber-200 hover:border-amber-400 rounded-xl hover:bg-amber-600 hover:text-white shadow-sm transition-all duration-200"
                                                                    title="{{ __('Edit Item') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <!-- Delete Button -->
                                                            <form action="{{ route('projects.risk-management.items.delete', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item risiko ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="p-2 text-rose-600 bg-rose-50 border border-rose-200 hover:border-rose-400 rounded-xl hover:bg-rose-600 hover:text-white shadow-sm transition-all duration-200"
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

                    <!-- Notes Form -->
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-6 shadow-sm">
                        <h4 class="font-bold text-sm text-primaryText mb-3 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-primary"></i>
                            {{ __('Catatan Umum Risk Management') }}
                        </h4>
                        <form action="{{ route('projects.risk-management.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <textarea name="notes" rows="3" class="w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-primary focus:ring focus:ring-primary/20 mb-3" placeholder="Masukkan catatan tambahan mengenai manajemen risiko proyek ini... ">{{ old('notes', $riskPlan->notes) }}</textarea>
                            <button type="submit" class="w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold shadow-md transition duration-200">
                                {{ __('Simpan Catatan') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: AI Assistant Recommendations Sidebar -->
                <div class="w-full lg:w-80 xl:w-96 shrink-0 space-y-6">
                    <div class="bg-white rounded-xl border border-[#e3e3e0] p-5 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h4 class="font-bold text-sm text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-robot text-blue-600"></i>
                                {{ __('Rekomendasi AI') }}
                            </h4>
                            <form action="{{ route('projects.risk-management.generate_ai', $project->id) }}" method="POST" onsubmit="document.getElementById('ai-spinner').classList.remove('hidden'); document.getElementById('ai-btn-text').innerText='{{ __('Menganalisis...') }}';">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-600 hover:text-white rounded-xl text-[10px] font-bold transition-all duration-200 flex items-center gap-1.5 shadow-sm">
                                    <i id="ai-spinner" class="fas fa-spinner fa-spin hidden"></i>
                                    <span id="ai-btn-text">{{ __('Generate AI') }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- Suggestions List -->
                        @if(empty($aiSuggestions))
                            <div class="text-center py-12">
                                <div class="w-12 h-12 bg-gray-50 text-gray-400 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                                    <i class="fas fa-lightbulb text-lg"></i>
                                </div>
                                <p class="text-xs text-gray-400 italic px-4 leading-relaxed">{{ __('Belum ada rekomendasi AI. Klik tombol di atas untuk menganalisis risiko proyek.') }}</p>
                            </div>
                        @else
                            <div class="space-y-4 max-h-[720px] overflow-y-auto pr-1">
                                @foreach($aiSuggestions as $idx => $sug)
                                    <div class="p-4 bg-gray-50 border border-gray-200/60 rounded-2xl hover:border-blue-300 hover:bg-blue-50/10 transition-all duration-300 hover:shadow-sm space-y-3">
                                        <div class="flex items-center justify-between gap-2 pb-2 border-b border-gray-100">
                                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full shrink-0">Saran #{{ $idx + 1 }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold font-mono tracking-wider shrink-0 uppercase">{{ $sug['probability'] ?? 'medium' }} / {{ $sug['severity'] ?? 'medium' }}</span>
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-xs text-primaryText leading-snug">{{ $sug['risk_title'] ?? '-' }}</h5>
                                            <p class="text-[10px] text-gray-500 leading-relaxed mt-1">{{ $sug['risk_description'] ?? '-' }}</p>
                                        </div>
                                        
                                        @if(!empty($sug['risk_cause']))
                                            <div class="text-[10px] text-secondaryText bg-white p-2 rounded-xl border border-gray-100">
                                                <span class="font-bold block text-[9px] uppercase tracking-wider text-gray-400 mb-0.5">Penyebab:</span>
                                                {{ $sug['risk_cause'] }}
                                            </div>
                                        @endif

                                        <div class="space-y-1.5 text-[10px]">
                                            <div class="bg-emerald-50/20 p-2 rounded-lg border border-emerald-100/50">
                                                <span class="font-bold block text-[9px] text-emerald-800 uppercase tracking-wider mb-0.5">Mitigasi:</span>
                                                {{ $sug['mitigation_plan'] ?? '-' }}
                                            </div>
                                            @if(!empty($sug['contingency_plan']))
                                                <div class="bg-blue-50/20 p-2 rounded-lg border border-blue-100/50">
                                                    <span class="font-bold block text-[9px] text-blue-800 uppercase tracking-wider mb-0.5">Kontingensi:</span>
                                                    {{ $sug['contingency_plan'] }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-2">
                                            <span class="text-[9px] text-secondaryText font-bold truncate max-w-[140px]" title="Owner: {{ $sug['risk_owner'] ?? '-' }}">Owner: {{ $sug['risk_owner'] ?? '-' }}</span>
                                            <button type="button" 
                                                    data-title="{{ $sug['risk_title'] ?? '' }}"
                                                    data-description="{{ $sug['risk_description'] ?? '' }}"
                                                    data-cause="{{ $sug['risk_cause'] ?? '' }}"
                                                    data-impact="{{ $sug['impact'] ?? '' }}"
                                                    data-probability="{{ $sug['probability'] ?? 'medium' }}"
                                                    data-severity="{{ $sug['severity'] ?? 'medium' }}"
                                                    data-mitigation="{{ $sug['mitigation_plan'] ?? '' }}"
                                                    data-contingency="{{ $sug['contingency_plan'] ?? '' }}"
                                                    data-owner="{{ $sug['risk_owner'] ?? '' }}"
                                                    onclick="applySuggestionFromBtn(this)" 
                                                    class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[9px] font-bold shadow-sm transition-all duration-200 hover:shadow shrink-0">
                                                <i class="fas fa-check-circle mr-0.5"></i> Gunakan Saran
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: ADD RISK ITEM -->
    <div id="add-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeAddModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('projects.risk-management.items.add', $project->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-plus text-primary"></i>
                                {{ __('Tambah Potensi Risiko') }}
                            </h3>
                            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Judul Risiko -->
                            <div>
                                <label for="add_risk_title" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Judul Risiko') }}</label>
                                <input type="text" name="risk_title" id="add_risk_title" required placeholder="Contoh: Keterlambatan Integrasi API Pihak Ketiga" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Deskripsi Risiko -->
                            <div>
                                <label for="add_risk_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Risiko') }}</label>
                                <textarea name="risk_description" id="add_risk_description" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Jelaskan detail mengenai skenario risiko..."></textarea>
                            </div>

                            <!-- Penyebab Risiko -->
                            <div>
                                <label for="add_risk_cause" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Faktor Penyebab (Cause) (Optional)') }}</label>
                                <textarea name="risk_cause" id="add_risk_cause" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Penyebab utama dari risiko tersebut..."></textarea>
                            </div>

                            <!-- Dampak -->
                            <div>
                                <label for="add_impact" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Dampak Risiko (Impact)') }}</label>
                                <textarea name="impact" id="add_impact" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Dampak yang dirasakan pada jadwal/biaya/tim jika terjadi..."></textarea>
                            </div>

                            <!-- Probability & Severity & Status (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="add_probability" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Probabilitas') }}</label>
                                    <select name="probability" id="add_probability" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add_severity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keparahan') }}</label>
                                    <select name="severity" id="add_severity" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="add_status" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Status') }}</label>
                                    <select name="status" id="add_status" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="open">Open</option>
                                        <option value="mitigated">Mitigated</option>
                                        <option value="accepted">Accepted</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rencana Mitigasi -->
                            <div>
                                <label for="add_mitigation_plan" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Rencana Mitigasi (Preventif)') }}</label>
                                <textarea name="mitigation_plan" id="add_mitigation_plan" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Tindakan pencegahan sebelum risiko terjadi..."></textarea>
                            </div>

                            <!-- Rencana Kontingensi -->
                            <div>
                                <label for="add_contingency_plan" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Rencana Kontingensi (Reaktif) (Optional)') }}</label>
                                <textarea name="contingency_plan" id="add_contingency_plan" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Langkah pemulihan jika risiko benar-benar terjadi..."></textarea>
                            </div>

                            <!-- WBS Link & Owner (Grid) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="add_related_wbs_item_id" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Tautkan WBS (Optional)') }}</label>
                                    <select name="related_wbs_item_id" id="add_related_wbs_item_id" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">-- {{ __('Tidak ditautkan') }} --</option>
                                        @foreach($wbsItems as $wbs)
                                            <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="add_risk_owner" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Pemilik Risiko (Owner)') }}</label>
                                    <input type="text" name="risk_owner" id="add_risk_owner" placeholder="Contoh: Project Manager, Developer" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="add_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keterangan Lain (Opsional)') }}</label>
                                <textarea name="notes" id="add_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Catatan internal..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            {{ __('Simpan Risiko') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT RISK ITEM -->
    <div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeEditModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-item-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-primaryText flex items-center gap-1.5">
                                <i class="fas fa-edit text-amber-500"></i>
                                {{ __('Ubah Potensi Risiko') }}
                            </h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-1">
                            <!-- Judul Risiko -->
                            <div>
                                <label for="edit_risk_title" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Judul Risiko') }}</label>
                                <input type="text" name="risk_title" id="edit_risk_title" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                            </div>

                            <!-- Deskripsi Risiko -->
                            <div>
                                <label for="edit_risk_description" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Deskripsi Risiko') }}</label>
                                <textarea name="risk_description" id="edit_risk_description" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
                            </div>

                            <!-- Penyebab Risiko -->
                            <div>
                                <label for="edit_risk_cause" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Faktor Penyebab (Cause) (Optional)') }}</label>
                                <textarea name="risk_cause" id="edit_risk_cause" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
                            </div>

                            <!-- Dampak -->
                            <div>
                                <label for="edit_impact" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Dampak Risiko (Impact)') }}</label>
                                <textarea name="impact" id="edit_impact" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
                            </div>

                            <!-- Probability & Severity & Status (Grid) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="edit_probability" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Probabilitas') }}</label>
                                    <select name="probability" id="edit_probability" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_severity" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keparahan') }}</label>
                                    <select name="severity" id="edit_severity" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_status" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Status') }}</label>
                                    <select name="status" id="edit_status" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="open">Open</option>
                                        <option value="mitigated">Mitigated</option>
                                        <option value="accepted">Accepted</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rencana Mitigasi -->
                            <div>
                                <label for="edit_mitigation_plan" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Rencana Mitigasi (Preventif)') }}</label>
                                <textarea name="mitigation_plan" id="edit_mitigation_plan" required rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
                            </div>

                            <!-- Rencana Kontingensi -->
                            <div>
                                <label for="edit_contingency_plan" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Rencana Kontingensi (Reaktif) (Optional)') }}</label>
                                <textarea name="contingency_plan" id="edit_contingency_plan" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
                            </div>

                            <!-- WBS Link & Owner (Grid) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="edit_related_wbs_item_id" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Tautkan WBS (Optional)') }}</label>
                                    <select name="related_wbs_item_id" id="edit_related_wbs_item_id" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">-- {{ __('Tidak ditautkan') }} --</option>
                                        @foreach($wbsItems as $wbs)
                                            <option value="{{ $wbs->id }}">{{ $wbs->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_risk_owner" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Pemilik Risiko (Owner)') }}</label>
                                    <input type="text" name="risk_owner" id="edit_risk_owner" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label for="edit_notes" class="block text-xs font-bold text-primaryText uppercase tracking-wider mb-1">{{ __('Keterangan Lain (Opsional)') }}</label>
                                <textarea name="notes" id="edit_notes" rows="2" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"></textarea>
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

    <!-- MODAL TOGGLERS & SUGGESTION HANDLERS -->
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

        function openEditModalFromBtn(btn) {
            try {
                const item = JSON.parse(btn.getAttribute('data-item'));
                const modal = document.getElementById('edit-modal');
                const form = document.getElementById('edit-item-form');

                // Fill prefilled values
                document.getElementById('edit_risk_title').value = item.risk_title || '';
                document.getElementById('edit_risk_description').value = item.risk_description || '';
                document.getElementById('edit_risk_cause').value = item.risk_cause || '';
                document.getElementById('edit_impact').value = item.impact || '';
                document.getElementById('edit_probability').value = item.probability || 'medium';
                document.getElementById('edit_severity').value = item.severity || 'medium';
                document.getElementById('edit_status').value = item.status || 'open';
                document.getElementById('edit_mitigation_plan').value = item.mitigation_plan || '';
                document.getElementById('edit_contingency_plan').value = item.contingency_plan || '';
                document.getElementById('edit_related_wbs_item_id').value = item.related_wbs_item_id || '';
                document.getElementById('edit_risk_owner').value = item.risk_owner || '';
                document.getElementById('edit_notes').value = item.notes || '';

                // Update action route dynamically
                form.action = `/projects/{{ $project->id }}/risk-management/items/${item.id}`;

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } catch (err) {
                console.error("Error parsing edit item data: ", err);
            }
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function applySuggestionFromBtn(btn) {
            // Open add modal
            openAddModal();

            // Populate form values from button data attributes
            document.getElementById('add_risk_title').value = btn.getAttribute('data-title') || '';
            document.getElementById('add_risk_description').value = btn.getAttribute('data-description') || '';
            document.getElementById('add_risk_cause').value = btn.getAttribute('data-cause') || '';
            document.getElementById('add_impact').value = btn.getAttribute('data-impact') || '';
            document.getElementById('add_probability').value = btn.getAttribute('data-probability') || 'medium';
            document.getElementById('add_severity').value = btn.getAttribute('data-severity') || 'medium';
            document.getElementById('add_mitigation_plan').value = btn.getAttribute('data-mitigation') || '';
            document.getElementById('add_contingency_plan').value = btn.getAttribute('data-contingency') || '';
            document.getElementById('add_risk_owner').value = btn.getAttribute('data-owner') || '';
        }
    </script>
</x-app-layout>
