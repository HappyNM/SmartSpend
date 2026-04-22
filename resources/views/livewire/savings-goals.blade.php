<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Savings Goals</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        @include('livewire.wallet-partials.goal-form')
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Your Goals</h2>

        <div class="space-y-3">
            @forelse($goals as $goal)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $goal->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $goal->description }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ strtoupper($goal->status) }}
                        </span>
                    </div>
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                        Saved: KES {{ number_format($goal->current_amount, 2) }}
                        @if($goal->target_amount)
                            / KES {{ number_format($goal->target_amount, 2) }}
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No savings goals yet.</p>
            @endforelse
        </div>
    </div>
</div>