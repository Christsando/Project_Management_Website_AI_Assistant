{{-- <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl max-w-md">
    <a href="{{ route('projects.human-resource.show', $project->id) }}"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg bg-white text-slate-800 shadow-sm transition">
        {{ __('Human Resource') }}
    </a>
    <a href="{{ route('projects.timeline.show', $project->id) }}"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition">
        {{ __('Gantt Chart') }}
    </a>
    <a href="{{ route('projects.budget.show', $project->id) }}"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg text-slate-500 hover:text-slate-700 transition">
        {{ __('Budgeting') }}
    </a>
</div> --}}

@php
    $tab = request('tab', 'hr');
@endphp

<div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl max-w-md">
    
    <a href="{{ route('projects.human-resource.show', $project->id) }}?tab=hr"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition
        {{ $tab === 'hr' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        {{ __('Human Resource') }}
    </a>

    <a href="{{ route('projects.human-resource.show', $project->id) }}?tab=gantt"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition
        {{ $tab === 'gantt' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        {{ __('Gantt Chart') }}
    </a>

    <a href="{{ route('projects.human-resource.show', $project->id) }}?tab=budget"
        class="flex-1 py-1.5 text-center text-xs font-bold rounded-lg transition
        {{ $tab === 'budget' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        {{ __('Budgeting') }}
    </a>

</div>