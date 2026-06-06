<table class="w-full text-left border-collapse">
    <thead>
        <tr
            class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            <th class="py-4 px-6">{{ __('ITEM WBS') }}</th>
            <th class="py-4 px-4">{{ __('PERAN') }}</th>
            <th class="py-4 px-4">{{ __('KEAHLIAN') }}</th>
            <th class="py-4 px-4">{{ __('PERSONIL (PIC)') }}</th>
            <th class="py-4 px-4 text-center">{{ __('WORKLOAD %') }}</th>
            <th class="py-4 px-6 text-right">{{ __('DURASI') }}</th>
            <th class="py-4 px-6 text-right pr-6">{{ __('AKSI') }}</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-50 text-xs">
        @foreach ($hrItems as $item)
            @php
                $meta = $item->workload_meta;
            @endphp
            <tr class="hover:bg-slate-50/30 transition duration-150">
                <td class="py-4 px-6 max-w-[160px]">
                    @if ($item->wbsItem)
                        <div class="font-extrabold text-slate-800 text-sm truncate" title="{{ $item->wbsItem->title }}">
                            {{ $item->wbsItem->title }}
                        </div>
                        <div class="text-[9px] text-slate-400 font-bold mt-0.5 uppercase tracking-wider">
                            @if ($item->wbsItem->parent)
                                Fase {{ $item->wbsItem->parent->title }}
                            @else
                                Fase Perencanaan
                            @endif
                        </div>
                    @else
                        <span class="text-slate-400 italic text-[10px]">-</span>
                    @endif
                </td>
                <td class="py-4 px-4 text-slate-700 font-bold">
                    {{ $item->role_name }}
                </td>
                <td class="py-4 px-4 max-w-[180px]">
                    <div class="flex flex-wrap gap-1">
                        @foreach ($item->skills as $skill)
                            @if (!empty($skill))
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-750 border border-purple-100">
                                    {{ $skill }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </td>
                <td class="py-4 px-4">
                    @if ($item->teamMember)
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-full bg-[#0B1329] text-white flex items-center justify-center font-black text-[9px] shadow-sm shrink-0">
                                {{ getInitials($item->teamMember->name) }}
                            </div>
                            <span
                                class="font-bold text-slate-700 truncate max-w-[120px]">{{ $item->teamMember->name }}</span>
                        </div>
                    @elseif($item->person_in_charge)
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-[9px] shadow-sm shrink-0">
                                {{ getInitials($item->person_in_charge) }}
                            </div>
                            <span
                                class="font-bold text-slate-600 truncate max-w-[120px]">{{ $item->person_in_charge }}</span>
                        </div>
                    @else
                        <span class="text-slate-400 italic text-[10px]">{{ __('Belum ditentukan') }}</span>
                    @endif
                </td>
                <td class="py-4 px-4">
                    <div class="w-24 mx-auto">
                        <div class="flex items-center justify-between text-[10px] font-bold text-slate-700 mb-1">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden flex-1 mr-2 border border-slate-200/50">
                                <div class="h-full rounded-full {{ $item->workloadMeta['barColor'] }}"
                                    style="width: {{ $item->workload_percentage ?? 0 }}%">
                                </div>
                            </div>
                            <span class="font-mono">{{ $item->workload_percentage ?? 0 }}%</span>
                        </div>

                        <span class="text-[8px] font-black uppercase tracking-wider block text-left {{ $item->workloadMeta['labelClass'] }}">
                            {{ $item->workloadMeta['label'] }}
                        </span>
                    </div>
                </td>
                <td class="py-4 px-6 text-right font-extrabold text-slate-800 font-mono text-sm">
                    @if ($item->estimated_work_days)
                        {{ $item->estimated_work_days }} Hari
                    @else
                        -
                    @endif
                </td>
                <td class="py-4 px-6 text-right pr-6">
                    <!-- Dropdown Ellipsis Menu using Alpine.js -->
                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open"
                            class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition">
                            <i class="fas fa-ellipsis-v text-xs"></i>
                        </button>
                        <div x-show="open"
                            class="origin-top-right absolute right-0 mt-1 w-32 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 focus:outline-none divide-y divide-slate-55"
                            style="display: none;">
                            <div class="py-1">
                                <button type="button" onclick='openEditModal({!! json_encode($item) !!})'
                                    class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs text-amber-700 hover:bg-slate-50 transition font-bold">
                                    <i class="fas fa-edit text-[10px]"></i>
                                    {{ __('Ubah') }}
                                </button>
                            </div>
                            <div class="py-1">
                                <form
                                    action="{{ route('projects.human-resource.items.delete', [$project->id, $item->id]) }}"
                                    method="POST" class="w-full"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus item perencanaan SDM ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-slate-50 transition font-bold">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                        {{ __('Hapus') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
