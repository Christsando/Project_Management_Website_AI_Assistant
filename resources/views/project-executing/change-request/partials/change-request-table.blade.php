<div class="mt-4 bg-cardSection w-full rounded-2xl p-6">
    <h1 class="card-title text-black">Change Request List</h1>

    <div class="mt-4 max-h-[400px] overflow-y-auto">
        <table class="w-full">
            <thead class="border-b sticky top-0 bg-cardSection z-10">
                <tr class="text-slate-400 text-sm border-b">
                    <th class="w-10 px-2 text-center">No.</th>
                    <th class="text-left px-2">Task</th>
                    <th class="text-left px-2">Before</th>
                    <th class="text-left px-2">After</th>
                    <th class="px-2 text-center">Status</th>
                    <th class="text-left px-2">Requested By</th>
                    <th class="w-32 px-2 text-center">Tanggal</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($changeRequests as $index => $cr)
                    <tr class="border-b hover:bg-slate-50 text-sm cursor-pointer"
                        onclick="openChangeRequestDetail({
                            title: @js($cr->wbsItem->title ?? '-'),
                            old_value: @js($cr->old_value ?? '-'),
                            new_value: @js($cr->new_value ?? '-'),
                            reason: @js($cr->reason ?? '-'),
                            status: @js($cr->status ?? '-'),
                            requested_by: @js($cr->requestedBy->name ?? '-'),
                            date: @js($cr->created_at?->format('d M Y') ?? '-')
                        })">
                        <td class="text-center py-2">{{ $index + 1 }}</td>

                        <td class="px-2 py-2 font-medium">
                            {{ $cr->wbsItem->title ?? '-' }}
                        </td>

                        <td class="px-2 py-2 text-slate-500 max-w-[160px] truncate" title="{{ $cr->old_value }}">
                            {{ $cr->old_value ?? '-' }}
                        </td>

                        <td class="px-2 py-2 text-slate-700 max-w-[160px] truncate" title="{{ $cr->new_value }}">
                            {{ $cr->new_value ?? '-' }}
                        </td>

                        <td class="px-2 py-2 text-center">
                            <span
                                class="inline-block py-1 px-2 rounded-md text-xs
                                @if ($cr->status == 'pending') bg-orangeStatus border border-gradientOrange text-gradientOrange
                                @elseif($cr->status == 'approved') bg-blueStatus border border-gradientBlue text-gradientBlue
                                @elseif($cr->status == 'rejected') bg-red-400 border border-red-700 text-red-900
                                @else bg-gray-400 @endif">
                                {{ ucfirst($cr->status ?? '-') }}
                            </span>
                        </td>

                        <td class="px-2 py-2">
                            {{ $cr->requestedBy->name ?? '-' }}
                        </td>

                        <td class="text-center py-2">
                            {{ $cr->created_at?->format('d M Y') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-slate-400 text-sm">
                            Belum ada change request untuk project ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>