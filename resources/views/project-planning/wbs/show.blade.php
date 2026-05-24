<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6 max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <a href="{{ route('project-planning.wbs.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 mb-2 transition gap-1">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Kembali ke Daftar WBS') }}
                    </a>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Detail Work Breakdown Structure (WBS)') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        {{ __('Proyek:') }} <span class="font-bold text-primaryText">{{ $project->title }}</span>
                    </h3>
                </div>

                @php
                    $userRole = strtolower(Auth::user()->role);
                    $isPmo = ($userRole === 'pmo' || $userRole === 'project management officer');
                @endphp

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 hover:text-gray-900 rounded-xl text-xs font-semibold shadow-sm transition gap-1.5">
                        <i class="fas fa-project-diagram text-gray-400"></i>
                        {{ __('Hub Proyek') }}
                    </a>

                    @if($isPmo && !$isWbsFinalized)
                        <a href="{{ route('projects.wbs.create', $project->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-blue-500/10 transition gap-1.5">
                            <i class="fas fa-plus"></i>
                            {{ __('Tambah Item WBS') }}
                        </a>

                        @if($totalItems > 0)
                            <form action="{{ route('projects.wbs.finalize', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi WBS ini? Setelah finalized, seluruh item WBS akan dikunci dan tidak dapat diubah atau dihapus.');">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md hover:shadow-lg shadow-emerald-500/10 transition gap-1.5">
                                    <i class="fas fa-check-double"></i>
                                    {{ __('Finalisasi WBS') }}
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- WBS Finalized Banner -->
            @if($isWbsFinalized)
                <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-white border border-blue-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl border border-blue-200/50 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-check-double text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">{{ __('Work Breakdown Structure (WBS) Finalized') }}</h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed font-semibold">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            {{ __('Siap digunakan untuk Timeline Planning.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-2xl border border-gray-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-file-signature text-gray-500"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ __('Draf WBS') }}</h4>
                        <p class="text-xs text-secondaryText mt-1 leading-relaxed">
                            {{ __('Status WBS masih berupa draf. PMO dapat menambah, mengubah, atau menghapus item WBS. Finalisasikan struktur WBS setelah selesai.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Scope Reference Card -->
            <div class="mb-6 bg-emerald-50/20 border border-emerald-100 p-5 rounded-2xl">
                <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fas fa-sitemap"></i>
                    {{ __('Referensi Tujuan Project Scope') }}
                </h4>
                <p class="text-sm text-primaryText leading-relaxed">
                    {{ $project->scope->objective }}
                </p>
            </div>

            <!-- WBS Hierarchy Tree Table -->
            <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                @if($wbsItems->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-list-ol text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Belum ada item WBS') }}</h4>
                        <p class="text-sm text-secondaryText mb-4">{{ __('Struktur kerja WBS belum dibuat untuk proyek ini.') }}</p>
                        @if($isPmo)
                            <a href="{{ route('projects.wbs.create', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md transition gap-2">
                                <i class="fas fa-plus text-[10px]"></i>
                                {{ __('Tambah Item WBS Pertama') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-[#e3e3e0] text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                    <th class="px-6 py-4">{{ __('Item WBS') }}</th>
                                    <th class="px-6 py-4">{{ __('Deskripsi') }}</th>
                                    <th class="px-6 py-4">{{ __('Hasil (Deliverable)') }}</th>
                                    <th class="px-6 py-4">{{ __('Prioritas') }}</th>
                                    <th class="px-6 py-4">{{ __('Estimasi (Hari)') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($wbsItems as $item)
                                    @include('project-planning.wbs._row', ['item' => $item, 'depth' => 0, 'project' => $project, 'isWbsFinalized' => $isWbsFinalized])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
