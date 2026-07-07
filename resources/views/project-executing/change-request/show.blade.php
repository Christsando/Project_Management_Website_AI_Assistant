<x-app-layout>
    <x-slot name="header">
        <x-header-component mode="changeRequest" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-full shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ __('CHANGE REQUEST') }}
                </div>
                <h1 class="font-semibold text-3xl">{{ __('Change Request') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Need to evaluate about the task? Let us help!') }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            @include('project-executing.change-request.partials.change-request-table', [
                'changeRequests' => $changeRequests,
            ])
        </div>
    </div>
    @include('project-executing.change-request.partials.change-request-modal')
    <script>
        window.openChangeRequestDetail = function(cr) {
            document.getElementById('crTaskTitle').innerText = cr.title;
            document.getElementById('crOldValue').innerText = cr.old_value;
            document.getElementById('crNewValue').innerText = cr.new_value;
            document.getElementById('crReason').innerText = cr.reason;
            document.getElementById('crStatus').innerText = cr.status;
            document.getElementById('crDate').innerText = cr.date;
            document.getElementById('crRequestedBy').innerText = cr.requested_by;
            document.getElementById('changeRequestDetailModal').classList.remove('hidden');
            document.getElementById('crRequestedDeadline').innerText = cr.requested_deadline ?? '-';
        };

        function closeChangeRequestDetailModal() {
            document.getElementById('changeRequestDetailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
