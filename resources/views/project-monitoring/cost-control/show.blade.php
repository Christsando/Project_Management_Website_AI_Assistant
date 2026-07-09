<x-app-layout>
    <x-slot name="header">
        <x-header-component mode="costControl" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 min-h-full h-fit shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6 flex justify-between items-center border-b border-slate-100 pb-5">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <a href="{{ route('cost-control.index') }}"
                        class="text-[10px] hover:text-slate-500 font-bold text-slate-400 uppercase tracking-widest">
                        <i class="fas fa-arrow-left text-[8px]"></i>
                        {{ __('Kembali | ') }}
                    </a>
                    {{ __('COST CONTROL') . __(' / ') . __($project->title ?? '-') }}
                </div>
                <h1 class="font-semibold text-3xl">{{ __('Cost Control') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Monitoring your ptoject budget here.') }}</p>
            </div>
        </div>

        <div class="flex gap-6 mb-4">
            <!-- Cost Breakdown -->
            <div class="flex-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-xl">
                        Cost Breakdown
                    </h3>

                    <button type="button" onclick="openAddModal()"
                        class="flex items-center justify-center px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition gap-1.5">
                        <i class="fas fa-plus text-xs"></i> Add Item
                    </button>
                </div>

                @include('project-monitoring.cost-control.partials.budget-table')
            </div>

            <!-- KPI -->
            <div class="w-80 shrink-0">
                <div class="grid gap-4">

                    <div
                        class="rounded-xl p-4 shadow-sm bg-gradient-to-br from-green-500 to-emerald-600 text-white hover:scale-[1.01] transition-all">
                        <p class="text-sm text-white/80">Remaining</p>
                        <h2 class="text-2xl font-black">
                            Rp {{ number_format($remaining, 0, ',', '.') }},-
                        </h2>
                    </div>

                    <div
                        class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm hover:scale-[1.01] transition-all">
                        <p class="text-sm text-slate-500">Planned Budget</p>
                        <h2 class="text-2xl font-bold">
                            Rp {{ number_format($planned, 0, ',', '.') }},-
                        </h2>
                    </div>

                    <div
                        class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm hover:scale-[1.01] transition-all">
                        <p class="text-sm text-slate-500">Actual Cost</p>
                        <h2 class="text-2xl font-bold">
                            Rp {{ number_format($actual, 0, ',', '.') }},-
                        </h2>
                    </div>

                    <div
                        class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm hover:scale-[1.01] transition-all">
                        <p class="text-sm text-slate-500">Usage</p>
                        <h2
                            class="text-2xl font-bold {{ $usage > 100 ? 'text-red-500' : ($usage > 80 ? 'text-yellow-500' : 'text-green-500') }}">
                            {{ number_format($usage, 0) }}%
                        </h2>
                    </div>
                </div>
            </div>
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
    </div>
    @include('project-monitoring.cost-control.partials.add-item-modal')
</x-app-layout>
<script>
    function openAddModal() {
        const modal = document.getElementById('add-modal');

        modal.classList.remove('hidden');
    }

    function closeAddModal() {
        const modal = document.getElementById('add-modal');

        modal.classList.add('hidden');
    }
</script>
