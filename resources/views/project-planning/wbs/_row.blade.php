<tr class="hover:bg-gray-50/50 transition">
    <td class="px-6 py-4">
        <div class="flex items-center" style="padding-left: {{ $depth * 24 }}px">
            @if($depth > 0)
                <span class="text-gray-400 mr-2 font-mono">↳</span>
            @endif
            <div>
                <span class="font-bold text-primaryText">{{ $item->title }}</span>
                <span class="text-[10px] text-secondaryText block font-mono">ID: #{{ $item->id }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 text-secondaryText max-w-xs truncate" title="{{ $item->description }}">
        {{ $item->description }}
    </td>
    <td class="px-6 py-4 text-secondaryText">
        {{ $item->deliverable ?: '-' }}
    </td>
    <td class="px-6 py-4">
        @php
            $priorityColors = [
                'low' => 'bg-gray-50 text-gray-800 border-gray-200',
                'medium' => 'bg-blue-50 text-blue-800 border-blue-200',
                'high' => 'bg-rose-50 text-rose-800 border-rose-200',
            ][$item->priority] ?? 'bg-gray-50 text-gray-800 border-gray-200';
        @endphp
        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold border {{ $priorityColors }}">
            {{ ucfirst($item->priority) }}
        </span>
    </td>
    <td class="px-6 py-4 text-secondaryText font-semibold">
        {{ $item->estimated_duration_days ? $item->estimated_duration_days . ' ' . __('Hari') : '-' }}
    </td>
    <td class="px-6 py-4 text-right">
        <div class="inline-flex gap-2">
            @if((strtolower(Auth::user()->role) === 'pmo' || strtolower(Auth::user()->role) === 'project management officer') && !$isWbsFinalized)
                <a href="{{ route('projects.wbs.edit', [$project->id, $item->id]) }}" class="inline-flex items-center p-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-600 hover:text-white shadow-sm transition" title="{{ __('Edit Item') }}">
                    <i class="fas fa-edit text-xs"></i>
                </a>
                
                @if($item->status === 'draft')
                    <form action="{{ route('projects.wbs.destroy', [$project->id, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item WBS ini? Menghapus item ini akan ikut menghapus seluruh sub-task di bawahnya.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center p-1.5 text-xs font-semibold text-rose-600 bg-rose-50/50 border border-rose-300 rounded-lg hover:bg-rose-600 hover:text-white hover:border-rose-600 shadow-sm transition" title="{{ __('Hapus Item') }}">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                @endif
            @else
                <span class="text-xs text-gray-400 italic">{{ __('Read-only') }}</span>
            @endif
        </div>
    </td>
</tr>

@foreach($item->children as $child)
    @include('project-planning.wbs._row', ['item' => $child, 'depth' => $depth + 1])
@endforeach
