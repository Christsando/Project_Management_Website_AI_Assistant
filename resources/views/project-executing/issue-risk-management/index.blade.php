<x-app-layout>
    <x-slot name="header">
        <x-header-component :projects="$projects" :showSearch="true" mode="issueRisk" />
    </x-slot>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 h-full shadow-sm p-6 max-w-full mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ __('ISSUE MANAGEMENT') }}
                </div>
                <h1 class="font-semibold text-3xl">{{ __('Issue Manajemen') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Track all issue here, don`t lost track!') }}</p>
            </div>
            <a onclick="openModal()"
                class="px-4 py-1 bg-gradient-to-br from-blue-600 to-gradientBlue hover:bg-blue-700 text-white rounded-xl text-lg transition shadow-blue-500/10 hover:shadow-lg cursor-pointer">
                <i class="fas fa-plus text-[10px]"></i>
                Add Issue
            </a>
        </div>

        <div class="flex items-center justify-between mb-6">
            {{-- @include('project-executing.issue-risk-management.partials.sub-navigation') --}}
            <div class="w-20 block">
            </div>
            <div class="flex items-center justify-center gap-3">
                @include('project-executing.issue-risk-management.partials.filter')
            </div>
        </div>

        <!-- content here -->
        @if (!is_null($issues))
            @include('project-executing.issue-risk-management.partials.issue-table', ['issues' => $issues])
        @else
            <div class="text-center py-32 text-slate-400">
                <i class="fas fa-folder text-5xl"></i>
                <p class="text-xl font-semibold">Belum ada project dipilih</p>
                <p class="text-sm">Silakan pilih project melalui search di atas</p>
            </div>
        @endif
    </div>
    @include('project-executing.issue-risk-management.partials.issue-form')
    @include('project-executing.issue-risk-management.partials.issue-detail')
</x-app-layout>
<script>
    document.getElementById('issue-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    function openModal() {
        document.getElementById('issue-modal').classList.remove('hidden');
        document.getElementById('issue-modal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('issue-modal').classList.add('hidden');
        document.getElementById('issue-modal').classList.remove('flex');
    }
</script>
