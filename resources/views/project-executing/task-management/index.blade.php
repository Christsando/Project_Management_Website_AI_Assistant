<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-full shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6">
            <h1 class="font-extrabold text-slate-800 leading-tight text-2xl">{{ __('Manajemen Task') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Manage all your project task with us!') }}</p>
        </div>

        @include('project-executing.task-management.partials.kanban-board')
    </div>
</x-app-layout>