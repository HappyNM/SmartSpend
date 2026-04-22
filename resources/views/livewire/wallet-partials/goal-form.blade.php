<form wire:submit="createGoal" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Goal Name</label>
        <input wire:model="name" type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
        <textarea wire:model="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lock Type</label>
            <select wire:model.live="lock_type" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                <option value="time">Time-based</option>
                <option value="amount">Amount-based</option>
                <option value="time_and_amount">Time + Amount</option>
            </select>
        </div>

        @if(in_array($lock_type, ['amount', 'time_and_amount']))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Amount</label>
                <input wire:model="target_amount" type="number" step="0.01" min="1" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                @error('target_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        @if(in_array($lock_type, ['time', 'time_and_amount']))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lock Until</label>
                <input wire:model="lock_until" type="date" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                @error('lock_until') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        @endif
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
        <input wire:model="allow_partial_withdrawal" type="checkbox" class="rounded border-gray-300 dark:border-gray-600">
        Allow partial withdrawal when unlocked
    </label>

    <button type="submit" class="px-4 py-3 rounded-lg bg-emerald-600 text-white font-semibold">
        Create Goal
    </button>
</form>