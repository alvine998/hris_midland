<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name *</label>
        <input id="name" type="text" name="name" value="{{ old('name', $package->name ?? '') }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug (auto if empty)</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $package->slug ?? '') }}" placeholder="my-package" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 @error('slug') border-red-500 @enderror">
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plan *</label>
        <select id="plan" name="plan" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
            @foreach ($plans as $p)<option value="{{ $p }}" @selected(old('plan', $package->plan ?? 'pro')===$p)>{{ ucfirst($p) }}</option>@endforeach
        </select>
        @error('plan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
        <textarea id="description" name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $package->description ?? '') }}</textarea>
    </div>
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Price (cents) *</label>
        <input id="price" type="number" name="price" value="{{ old('price', $package->price ?? 0) }}" min="0" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm @error('price') border-red-500 @enderror">
        <p class="text-xs text-gray-400 mt-1">e.g. 99000 = Rp 99k. Effective price shown after discount.</p>
        @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="discount_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Discount % (sale)</label>
        <input id="discount_percent" type="number" name="discount_percent" value="{{ old('discount_percent', $package->discount_percent ?? '') }}" min="0" max="100" placeholder="e.g. 20" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
        <p class="text-xs text-gray-400 mt-1">Sale price auto = price × (100 - discount)/100. Leave empty for no sale.</p>
        @error('discount_percent')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="sale_starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sale starts at</label>
        <input id="sale_starts_at" type="datetime-local" name="sale_starts_at" value="{{ old('sale_starts_at', isset($package) && $package->sale_starts_at ? $package->sale_starts_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
    </div>
    <div>
        <label for="sale_ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sale ends at</label>
        <input id="sale_ends_at" type="datetime-local" name="sale_ends_at" value="{{ old('sale_ends_at', isset($package) && $package->sale_ends_at ? $package->sale_ends_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
    </div>
    <div>
        <label for="max_employees" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Max employees</label>
        <input id="max_employees" type="number" name="max_employees" value="{{ old('max_employees', $package->max_employees ?? '') }}" min="1" placeholder="unlimited" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
    </div>
    <div>
        <label for="duration_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Duration (days)</label>
        <input id="duration_days" type="number" name="duration_days" value="{{ old('duration_days', $package->duration_days ?? '') }}" min="1" placeholder="e.g. 30" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true)) class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
        </label>
    </div>
</div>
