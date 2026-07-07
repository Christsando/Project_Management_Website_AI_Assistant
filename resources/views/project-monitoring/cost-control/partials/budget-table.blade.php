<table class="w-full text-sm">

    <thead>
        <tr class="border-b">
            <th class="text-left py-2">Category</th>
            <th class="text-right">Budget</th>
            <th class="text-right">Actual</th>
            <th class="text-right">Variance</th>
        </tr>
    </thead>

    <tbody>
        @forelse($breakdown as $category => $data)
            <tr class="border-b">
                <td class="py-2 capitalize">
                    {{ str_replace('_', ' ', $category) }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($data['planned'], 0, ',', '.') }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($data['actual'], 0, ',', '.') }}
                </td>
                <td class="text-right {{ $data['variance'] < 0 ? 'text-red-500' : 'text-green-500' }}">
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
