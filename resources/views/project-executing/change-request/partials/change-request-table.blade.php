<div class="bg-cardSection w-full rounded-2xl p-6">

    <div class="max-h-full overflow-y-auto">
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
                    <th class="w-32 px-2 text-center">Action</th>
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
                            requested_deadline: @js($cr->requested_deadline ? \Carbon\Carbon::parse($cr->requested_deadline)->format('d M Y') : '-'),
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

                        <td class="px-2 py-2 text-center">
                            @if (Auth::check() && strtolower(Auth::user()->role) === 'project management officer' && $cr->status == 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('change-request.approve', $cr->id) }}" method="POST"
                                        onsubmit="return confirm('Approve change request ini?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" name="status" value="approved"
                                            class="w-20 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('change-request.reject', $cr->id) }}" method="POST"
                                        onsubmit="return confirm('Reject change request ini?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="w-20 px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
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
