<x-app-layout>
    <x-slot name="header">
        <x-header-component :showSearch="true" mode="dashboard" />
    </x-slot>

    <div class="flex flex-col">
        <div class="bg-white rounded-2xl border-slate-100 border shadow-sm p-6 w-full h-full ">

            <!-- section menu title -->
            <div class="flex justify-between items-center pb-5">
                <div class="flex flex-col">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        {{ __('DASHBOARD' . ' / ' . 'PROJECT DETAIL' . ' - ' . $title) }}
                    </div>
                    <h1 class="font-semibold text-3xl">{{ $title ?? '-' }}</h1>
                    <p class="text-sm text-slate-500 mt-2">{{ $project->proposal->background ?? '-' }}</p>
                </div>
            </div>

            @include('dashboard.partials.project-date')

            <!-- General status section for first row-->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4 mt-6">
                @if ($showCards)
                    @foreach ($cards as $card)
                        <x-status-card :label="$card['label']" :titleColor="$card['titleColor']" :infoColor="$card['infoColor']" :valueColor="$card['valueColor']"
                            :value="$card['value']" :background="$card['background']" />
                    @endforeach
                @endif
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-2 rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                    <h1 class="card-title text-black">
                        Performa PIC / Team 
                    </h1>
                    @include('dashboard.partials.pic-table')
                </div>
                <div class="col-span-2 rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                    <h1 class="card-title text-black">
                        Task Breakdown
                    </h1>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
