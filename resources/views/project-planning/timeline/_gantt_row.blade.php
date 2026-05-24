<div class="grid grid-cols-12 gap-4 border-b border-gray-100 py-3.5 items-center hover:bg-gray-50/40 transition">
    <!-- WBS Item Name & Depth Indentation -->
    <div class="col-span-4">
        <div class="flex items-center" style="padding-left: {{ $depth * 16 }}px">
            @if($depth > 0)
                <span class="text-gray-400 mr-2 font-mono">↳</span>
            @endif
            <div class="truncate">
                <span class="font-bold text-xs text-primaryText" title="{{ $wbs->title }}">{{ $wbs->title }}</span>
                @if($wbs->timelineItem && $wbs->timelineItem->is_milestone)
                    <span class="text-[9px] bg-indigo-100 text-indigo-700 font-bold px-1 py-0.5 rounded ml-1" title="Milestone: {{ $wbs->timelineItem->milestone_name }}">
                        <i class="fas fa-flag text-[8px]"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Visual Gantt Bar Area -->
    <div class="col-span-8 relative h-8 flex items-center bg-gray-50/50 rounded-lg overflow-hidden border border-dashed border-gray-200">
        @if($wbs->timelineItem && $projectDurationDays > 0)
            @php
                $startDate = \Carbon\Carbon::parse($wbs->timelineItem->start_date);
                $endDate = \Carbon\Carbon::parse($wbs->timelineItem->end_date);
                
                $leftOffset = $minDate->diffInDays($startDate);
                $leftPercent = ($leftOffset / $projectDurationDays) * 100;
                
                $duration = $wbs->timelineItem->duration_days;
                $widthPercent = ($duration / $projectDurationDays) * 100;
                
                $barBg = $wbs->timelineItem->is_milestone ? 'bg-indigo-500 hover:bg-indigo-600' : 'bg-blue-500 hover:bg-blue-600';
            @endphp
            <div class="absolute h-5 rounded {{ $barBg }} flex items-center justify-center text-[10px] text-white font-bold shadow-sm transition-all" 
                 style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%; min-width: 24px;"
                 title="{{ $wbs->title }} ({{ $startDate->format('d M') }} - {{ $endDate->format('d M') }}, {{ $duration }} Hari)">
                 {{ $duration }}d
            </div>
        @else
            <span class="text-[10px] text-gray-400 italic pl-3">{{ __('Belum dijadwalkan') }}</span>
        @endif
    </div>
</div>

@foreach($wbs->children as $child)
    @include('project-planning.timeline._gantt_row', ['wbs' => $child, 'depth' => $depth + 1, 'projectDurationDays' => $projectDurationDays, 'minDate' => $minDate])
@endforeach
