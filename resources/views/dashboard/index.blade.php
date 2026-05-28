<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- section menu title -->
        <div class="py-2">
            <h1 class="font-semibold text-3xl">{{ __('Project Dashboard') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Plan, Prioritize, and accomplish your tasks with ease.') }}</p>
        </div>

        <!-- General status section for first row-->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @if ($showCards)
                @foreach ($cards as $card)
                    @include('dashboard.partials.' . $card)
                @endforeach
            @endif
        </div>

        <!-- Workflow Progress & Next Actions for second row-->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('dashboard.partials.project-analytic')
            @include('dashboard.partials.reminders')
        </div>
    </div>
</x-app-layout>