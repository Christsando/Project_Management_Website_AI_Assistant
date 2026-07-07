<x-app-layout>
    <x-slot name="header">
        <x-header-component mode="costControl" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-full shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ __('COST CONTROL') }}
                </div>
                <h1 class="font-semibold text-3xl">{{ __('Cost Control') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Monitoring your ptoject budget here.') }}</p>
            </div>
        </div>

        <div>
            @if (!$project)

                <!-- Belumpilihproject -->
                <div class="text-center py-32 text-slate-400">
                    <i class="fas fa-folder text-5xl"></i>
                    <p class="text-xl font-semibold">Belum ada project dipilih</p>
                    <p class="text-sm">Silakan pilih project melalui search di atas</p>
                </div>
            @else
                <!-- KPI -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-4 shadow">
                        <p class="text-slate-500 text-sm">Planned Budget</p>
                        <h2 class="text-2xl font-bold">
                            Rp {{ number_format($planned, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow">
                        <p class="text-slate-500 text-sm">Actual Cost</p>
                        <h2 class="text-2xl font-bold text-red-500">
                            Rp {{ number_format($actual, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow">
                        <p class="text-slate-500 text-sm">Remaining</p>
                        <h2 class="text-2xl font-bold text-green-500">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow">
                        <p class="text-slate-500 text-sm">Usage</p>
                        <h2 class="text-2xl font-bold {{ $usage > 100 ? 'text-red-500' : ($usage > 80 ? 'text-yellow-500' : 'text-green-500') }}">
                            {{ number_format($usage, 0) }}%
                        </h2>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="bg-white rounded-xl p-6 shadow mb-6">
                    <h3 class="font-semibold mb-4">
                        Cost Breakdown
                    </h3>

                    @include('project-monitoring.cost-control.partials.budget-table')
                </div>

                <!-- Alerts -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <h3 class="font-semibold text-red-600">
                        Cost Alerts
                    </h3>

                    <ul class="mt-2 text-sm list-disc pl-5">
                        @forelse($alerts as $alert)
                            <li class="text-red-500">{{ $alert }}</li>
                        @empty
                            <li class="text-green-500">Semua biaya masih aman</li>
                        @endforelse
                    </ul>
                </div>
            @endif
        </div>
    </div>


</x-app-layout>
