@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<div class="flex items-center">
    {{-- Bar aktif --}}
    @if($active ?? false)
        <div class="w-2 h-7 bg-blue-500 rounded-r-lg mr-3"></div>
    @else
        <div class="w-2 mr-3"></div> {{-- spacer biar konten tidak geser --}}
    @endif

    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</div>
