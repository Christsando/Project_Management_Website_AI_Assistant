<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
    <div class="h-[350px] overflow-y-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr
                    class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <td class="text-left py-2 px-2 w-14 text-center">No</td>
                    <td class="text-left py-2">Category</td>
                    <td class="text-left py-2">Description</td>
                    <td class="text-right pr-5">Budget</td>
                    <td class="text-right pr-5">Actual</td>
                    <td class="text-right pr-5">Variance</td>
                </tr>
            </thead>

            <tbody>
                @forelse($breakdown as $category => $data)
                    <tr class="border-b border-slate-100">
                        <td class="py-4 px-2 text-center capitalize">
                            {{ $loop->iteration }}
                        </td>
                        <td class="py-4 w-36 capitalize">
                            {{ str_replace('_', ' ', $category) }}
                        </td>
                        <td class="py-4 capitalize">
                            {{ str_replace('_', ' ', $data['description'] ) }}
                        </td>
                        <td class="w-42 pr-5 text-right">
                            Rp {{ number_format($data['planned'], 0, ',', '.') }}
                        </td>
                        <td class="w-42 pr-5 text-right">
                            Rp {{ number_format($data['actual'], 0, ',', '.') }}
                        </td>
                        <td class="w-42 pr-5 text-right {{ $data['variance'] < 0 ? 'text-red-500' : 'text-green-500' }}">
                            Rp {{ number_format($data['variance'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-slate-400">
                            Tidak ada data cost
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
