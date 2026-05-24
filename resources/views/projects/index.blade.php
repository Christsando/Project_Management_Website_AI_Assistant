<x-app-layout>
    <x-slot name="header">
        <x-header-component />
    </x-slot>

    <div class="pl-4 pt-4">
        <div class="bg-cardSection rounded-xl p-6">
            <!-- Header Section with Title and Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-primaryText leading-tight">
                        {{ __('Daftar Proyek') }}
                    </h2>
                    <h3 class="text-sm text-secondaryText mt-1">
                        @if(strtolower(Auth::user()->role) === 'project manager')
                            {{ __('Kelola proyek yang Anda usulkan dan ajukan proposal Anda.') }}
                        @elseif(in_array(strtolower(Auth::user()->role), ['pmo', 'project management officer']))
                            {{ __('Pantau proyek yang berada dalam fase Project Planning.') }}
                        @else
                            {{ __('Tinjau proposal proyek, ubah status persetujuan, dan jalankan fase perencanaan.') }}
                        @endif
                    </h3>
                </div>

                @if(strtolower(Auth::user()->role) === 'project manager')
                    <div>
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-md hover:shadow-lg shadow-blue-500/10 gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            {{ __('Tambah Proyek Baru') }}
                        </a>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table/List container -->
            <div class="bg-white rounded-xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                @if($projects->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-primaryText mb-1">{{ __('Tidak ada proyek ditemukan') }}</h4>
                        <p class="text-sm text-secondaryText">{{ __('Belum ada proyek yang dapat ditampilkan untuk peran Anda saat ini.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-[#e3e3e0] text-xs font-semibold text-secondaryText uppercase tracking-wider">
                                    <th class="px-6 py-4">{{ __('Judul Proyek') }}</th>
                                    <th class="px-6 py-4">{{ __('Pembuat') }}</th>
                                    <th class="px-6 py-4">{{ __('Manager Ditunjuk') }}</th>
                                    <th class="px-6 py-4">{{ __('Rentang Waktu') }}</th>
                                    <th class="px-6 py-4">{{ __('Status') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($projects as $project)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-primaryText">{{ $project->title }}</div>
                                            <div class="text-xs text-secondaryText line-clamp-1 max-w-xs mt-0.5">{{ $project->description ?: 'Tidak ada deskripsi.' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-secondaryText">
                                            {{ $project->owner ? $project->owner->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-secondaryText">
                                            {{ $project->manager ? $project->manager->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-secondaryText">
                                            @if($project->start_date && $project->end_date)
                                                {{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}
                                            @elseif($project->start_date)
                                                {{ __('Mulai: ') . $project->start_date->format('d M Y') }}
                                            @else
                                                <span class="text-gray-400 italic">{{ __('Belum diatur') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClasses = [
                                                    'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                    'submitted' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                    'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                                    'planning' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                ][$project->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex gap-2">
                                                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 shadow-sm transition">
                                                    <i class="fas fa-eye mr-1.5 text-gray-400"></i> {{ __('Detail') }}
                                                </a>

                                                @if(strtolower(Auth::user()->role) === 'project manager' && $project->owner_id === Auth::id() && in_array($project->status, ['draft', 'rejected']))
                                                    <a href="{{ route('projects.edit', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/50 border border-blue-300 rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-sm transition">
                                                        <i class="fas fa-edit mr-1.5"></i> {{ __('Edit') }}
                                                    </a>
                                                    
                                                    @if($project->status === 'draft')
                                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50/50 border border-rose-300 rounded-lg hover:bg-rose-600 hover:text-white hover:border-rose-600 shadow-sm transition">
                                                                <i class="fas fa-trash-alt mr-1.5"></i> {{ __('Hapus') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if(strtolower(Auth::user()->role) === 'manager')
                                                    <a href="{{ route('projects.edit', $project->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50/50 border border-amber-300 rounded-lg hover:bg-amber-600 hover:text-white hover:border-amber-600 shadow-sm transition">
                                                        <i class="fas fa-cog mr-1.5"></i> {{ __('Status') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
