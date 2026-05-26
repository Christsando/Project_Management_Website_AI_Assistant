<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    @include('project-executing.task-management.partials.kanban-board')
</x-app-layout>