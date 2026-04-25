<div class="min-h-screen bg-gray-50 dark:bg-neutral-900">
    <!-- header -->
    <div class="bg-emerald-800 dark:bg-emerald-900 shadow-xl border-b-4 border-yellow-400 rounded-b-2xl relative overflow-hidden">
        <!-- Optional subtle pattern overlay for uniqueness -->
        <div class="absolute inset-0 bg-white/10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
            <div class="flex items-center gap-4">
                <!-- Icon for visual interest -->
                <svg class="w-10 h-10 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <div>
                    <h1 class="text-4xl font-extrabold text-white drop-shadow-lg tracking-tight">Categories</h1>
                    <p class="text-yellow-100 mt-2 italic text-lg">Organize your expenses with custom categories</p>
                </div>
            </div>
        </div>
        <!-- Hover effect for interactivity -->
        <div class="absolute inset-0 bg-white/5 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
    </div>
    <!-- messages/ flash messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 dark:text-green-300 hover:text-green-800 dark:hover:text-green-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-600 dark:text-red-300 hover:text-red-800 dark:hover:text-red-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @endif
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- create/edit category form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-8">
                <h3 class="text-lg font-semibold text-gray-400 mb-6">
                    {{ $isEditing ? 'Edit Category' : 'Create Category' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Category Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-400 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            id="name"
                            wire:model="name"
                            placeholder="e.g., Food & Dining"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color Picker -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Color <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($colors as $colorOption)
                            <button type="button"
                                wire:click="$set('color', '{{ $colorOption }}')"
                                class="w-10 h-10 rounded-lg transition transform hover:scale-110 {{ $color === $colorOption ? 'ring-4 ring-offset-2 ring-gray-400' : '' }}"
                                @style(['background-color: ' . $colorOption])>
                            </button>
                            @endforeach
                        </div>
                        @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500">
                        <p class="text-sm text-gray-400 mb-2">Preview:</p>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium"
                            @style(['background-color: ' . $color . '20', 'color: ' . $color])>
                            <span class="w-3 h-3 rounded-full" @style(['background-color: ' . $color])></span>
                            {{ $name ?: 'Category Name' }}
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-2">
                        @if($isEditing)
                        <button type="button"
                            wire:click="cancelEdit"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancel
                        </button>
                        @endif
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 hover:shadow-lg transition">
                            {{ $isEditing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- categories listing -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Your Categories</h3>
                    <p class="text-sm text-gray-400 mt-1">{{ $categories->count() }} categories</p>
                </div>

                @if($categories->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($categories as $category)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition" wire:key="category-{{ $category->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    @style(['background-color: ' . $category->color . '20'])>
                                    <div class="w-6 h-6 rounded-full" @style(['background-color: ' . $category->color])></div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-100">{{ $category->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $category->expenses_count }} {{ Str::plural('expense', $category->expenses_count) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="edit({{ $category->id }})"
                                    class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                @if($category->expenses_count === 0)
                                <button wire:click="delete({{ $category->id }})"
                                    wire:confirm="Are you sure you want to delete this category?"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @else
                                <div class="p-2 text-gray-400 cursor-not-allowed" title="Cannot delete category with expenses">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Categories Yet</h3>
                    <p class="text-gray-600 dark:text-gray-300">Create your first category to start organizing your expenses.</p>
                </div>
                @endif
            </div>

            <!-- Default Categories Suggestion -->
            @if($categories->count() === 0)
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-3">💡 Suggested Categories</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm text-blue-800 dark:text-blue-300">
                    <div>🍔 Food & Dining</div>
                    <div>🏠 Housing</div>
                    <div>🚗 Transportation</div>
                    <div>🛒 Shopping</div>
                    <div>💊 Healthcare</div>
                    <div>🎬 Entertainment</div>
                    <div>✈️ Travel</div>
                    <div>📱 Utilities</div>
                    <div>🎓 Education</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

</div>