<tr class="hover:bg-gray-50/50 transition">
    <td class="px-6 py-4">
        <div class="flex items-center" style="padding-left: {{ $depth * 24 }}px">
            @if($depth > 0)
                <span class="text-gray-400 mr-2 font-mono">↳</span>
            @endif
            <div>
                <span class="font-bold text-primaryText">{{ $wbs->title }}</span>
                <span class="text-[10px] text-secondaryText block font-mono">WBS ID: #{{ $wbs->id }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-4">
        @if($wbs->timelineItem)
            <div class="text-sm font-semibold text-primaryText">
                {{ $wbs->timelineItem->start_date->format('d M Y') }} s/d {{ $wbs->timelineItem->end_date->format('d M Y') }}
            </div>
            @if($wbs->timelineItem->dependencyWbsItem)
                <div class="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/60 inline-block mt-1 font-semibold">
                    <i class="fas fa-link mr-1"></i>
                    Predecessor: #{{ $wbs->timelineItem->dependency_wbs_item_id }} ({{ Str::limit($wbs->timelineItem->dependencyWbsItem->title, 20) }})
                </div>
            @endif
        @else
            <span class="text-xs text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-200/50 inline-block font-semibold">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ __('Belum dijadwalkan') }}
            </span>
        @endif
    </td>
    <td class="px-6 py-4 font-semibold text-secondaryText">
        @if($wbs->timelineItem)
            {{ $wbs->timelineItem->duration_days }} {{ __('Hari') }}
        @else
            -
        @endif
    </td>
    <td class="px-6 py-4">
        @if($wbs->timelineItem && $wbs->timelineItem->is_milestone)
            <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                <i class="fas fa-flag text-[10px]"></i>
                {{ $wbs->timelineItem->milestone_name }}
            </span>
        @else
            <span class="text-gray-400 italic text-xs">-</span>
        @endif
    </td>
    <td class="px-6 py-4 text-right">
        <div class="inline-flex gap-2">
            @if((strtolower(Auth::user()->role) === 'pmo' || strtolower(Auth::user()->role) === 'project management officer') && !$isTimelineFinalized)
                @if($wbs->timelineItem)
                    <a href="{{ route('projects.timeline.edit', [$project->id, $wbs->timelineItem->id]) }}" class="inline-flex items-center p-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-600 hover:text-white shadow-sm transition" title="{{ __('Edit Jadwal') }}">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    
                    @if($wbs->timelineItem->status === 'draft')
                        <form action="{{ route('projects.timeline.destroy', [$project->id, $wbs->timelineItem->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal timeline untuk item ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center p-1.5 text-xs font-semibold text-rose-600 bg-rose-50/50 border border-rose-300 rounded-lg hover:bg-rose-600 hover:text-white hover:border-rose-600 shadow-sm transition" title="{{ __('Hapus Jadwal') }}">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('projects.timeline.create', $project->id) }}?wbs_id={{ $wbs->id }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white shadow-sm transition">
                        <i class="fas fa-calendar-plus mr-1"></i> {{ __('Jadwalkan') }}
                    </a>
                @endif
            @else
                <span class="text-xs text-gray-400 italic">{{ __('Read-only') }}</span>
            @endif
        </div>
    </td>
</tr>

@foreach($wbs->children as $child)
    @include('project-planning.timeline._timeline_row', ['wbs' => $child, 'depth' => $depth + 1])
@endforeach
