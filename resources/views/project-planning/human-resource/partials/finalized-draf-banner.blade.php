@if ($hrPlan)
    @if ($hrPlan->status === 'finalized')
        <div
            class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 shadow-sm flex items-center gap-3">
            <div
                class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold">{{ __('Siap digunakan untuk Risk Management') }}</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    {{ __('Data alokasi personil SDM Anda telah divalidasi secara permanen dan siap diintegrasikan dengan modul manajemen risiko.') }}
                </p>
            </div>
        </div>
    @else
        <div
            class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 shadow-sm flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold">{{ __('Draf Perencanaan SDM (Belum Final)') }}</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ __('Data alokasi personil SDM masih dalam status draf dan belum difinalisasi oleh PMO.') }}
                    </p>
                </div>
            </div>
            @if ($isPmo && $hrItems->count() > 0)
                <form action="{{ route('projects.human-resource.finalize', $project->id) }}" method="POST"
                    class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm transition gap-1.5">
                        <i class="fas fa-check-circle"></i>
                        {{ __('Finalisasi Perencanaan SDM') }}
                    </button>
                </form>
            @endif
        </div>
    @endif
@endif
