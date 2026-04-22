<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="border-b border-gray-200 dark:border-gray-700">
            <tr class="text-left text-xs uppercase text-gray-500 dark:text-gray-400">
                <th class="py-3 px-3">Date</th>
                <th class="py-3 px-3">Type</th>
                <th class="py-3 px-3">Status</th>
                <th class="py-3 px-3 text-right">Amount</th>
                <th class="py-3 px-3">Reference</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($transactions as $tx)
                <tr>
                    <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300">{{ $tx->created_at?->format('M d, Y H:i') }}</td>
                    <td class="py-3 px-3 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</td>
                    <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($tx->status) }}</td>
                    <td class="py-3 px-3 text-sm font-semibold text-right text-gray-900 dark:text-gray-100">
                        KES {{ number_format($tx->amount, 2) }}
                    </td>
                    <td class="py-3 px-3 text-sm text-gray-500 dark:text-gray-400">{{ $tx->reference ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>