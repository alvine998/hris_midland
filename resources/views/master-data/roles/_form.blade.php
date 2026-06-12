@php
    $selectedPermissions = old('rbac', $role->rbac ?? []);
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required placeholder="Role name" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
            <textarea name="description" rows="3" placeholder="Brief description" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('description') border-red-500 @enderror">{{ old('description', $role->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between gap-3 mb-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">RBAC Permissions</label>
                <p class="text-xs text-gray-500 dark:text-gray-400">Select the actions allowed for this role.</p>
            </div>
        </div>

        @error('rbac')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('rbac.*')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($permissionGroups as $group => $permissions)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ $group }}</p>
                    <div class="space-y-2">
                        @foreach ($permissions as $permission => $label)
                            <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="rbac[]" value="{{ $permission }}" @checked(in_array($permission, $selectedPermissions, true)) class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span>
                                    <span class="block font-medium">{{ $label }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $permission }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('master-data.roles') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</a>
        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">{{ $submitLabel }}</button>
    </div>
</div>
