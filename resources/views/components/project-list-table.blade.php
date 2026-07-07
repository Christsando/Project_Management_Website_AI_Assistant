@props([
    'projects',
    'route',
    'buttonText' => 'Pilih',
])

<table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
            <th class="px-6 py-4 w-16 text-center">No</th>
            <th class="px-6 py-4">Judul Proyek</th>
            <th class="px-6 py-4">Pemilik</th>
            <th class="px-6 py-4">Rentang Tanggal</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
        @forelse ($projects as $project)
            <tr class="hover:bg-slate-50 transition duration-150">
                <td class="px-6 py-4 text-center">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4 font-medium text-slate-800">
                    {{ $project->title }}
                </td>

                <td class="px-6 py-4">
                    {{ $project->owner->name ?? '-' }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                </td>

                <td class="px-6 py-4 text-center">
                    <a href="{{ route($route, $project) }}"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white transition hover:bg-indigo-700">
                        {{ $buttonText }}
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                    Belum ada data proyek.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>