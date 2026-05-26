@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex w-full items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all bg-blue-600 text-white shadow-md shadow-blue-500/10'
    : 'flex w-full items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900';
@endphp

<div class="flex items-center">

    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</div>
