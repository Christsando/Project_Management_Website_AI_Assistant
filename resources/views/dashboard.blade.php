<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">

        <div class="bg-cardSection rounded-xl">
            <!-- Title & subtitle section -->
            <div class="p-6">
                <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                    {{ __('Project Dashboard') }}
                </h2>
                <h3 class="text-sm text-secondaryText">
                    {{ __('Plan, Prioritize, and accomplish your tasks with ease.') }}
                </h3>
            </div>

            <div class="grid grid-cols-4"> 

            </div>
        </div>
    </div>
</x-app-layout>
