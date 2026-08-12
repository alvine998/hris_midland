<div class="space-y-5">
    <div>
        <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Question *</label>
        <input id="question" type="text" name="question" value="{{ old('question', $faq->question ?? '') }}" required maxlength="500" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 @error('question') border-red-500 @enderror">
        @error('question')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Answer *</label>
        <textarea id="answer" name="answer" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 @error('answer') border-red-500 @enderror">{{ old('answer', $faq->answer ?? '') }}</textarea>
        @error('answer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category</label>
            <input id="category" type="text" name="category" value="{{ old('category', $faq->category ?? '') }}" placeholder="e.g. Billing" maxlength="100" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sort order</label>
            <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
        </div>
        <div class="flex items-end pb-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true)) class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
            </label>
        </div>
    </div>
</div>
