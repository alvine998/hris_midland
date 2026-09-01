@php
    $selectedPermissions = old('rbac', $role->rbac ?? []);

    $crudActions = ['view', 'create', 'edit', 'delete'];
    $crudLabels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];

    $specialPermissions = [
        'System' => ['*' => 'Full Access'],
        'Tasks' => ['task.assign' => 'Assign Tasks', 'task.manage' => 'Manage All Tasks'],
        'Attendance' => ['attendance.import' => 'Import', 'attendance.export' => 'Export'],
    ];
@endphp

<div class="space-y-6" x-data="{
    selected: {{ json_encode($selectedPermissions) }},
    has(perm) { return this.selected.includes(perm) || this.selected.includes('*'); },
    toggle(perm) {
        if (perm === '*') {
            this.selected = this.selected.includes('*') ? [] : ['*'];
        } else {
            this.selected = this.selected.includes(perm)
                ? this.selected.filter(p => p !== perm)
                : [...this.selected.filter(p => p !== '*'), perm];
        }
    },
    toggleGroup(groupPerms) {
        const allSelected = groupPerms.every(p => this.selected.includes(p));
        if (allSelected) {
            this.selected = this.selected.filter(p => !groupPerms.includes(p));
        } else {
            this.selected = [...new Set([...this.selected, ...groupPerms])];
        }
    },
    groupCount(groupPerms) {
        if (this.selected.includes('*')) return groupPerms.length;
        return groupPerms.filter(p => this.selected.includes(p)).length;
    }
}">
    {{-- Name & Description --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required placeholder="Role name" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
            <textarea name="description" rows="2" placeholder="Brief description" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('description') border-red-500 @enderror">{{ old('description', $role->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Permissions Table --}}
    <div>
        <div class="flex items-center justify-between gap-3 mb-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
                <p class="text-xs text-gray-500 dark:text-gray-400">Toggle permissions for this role. Click group name to select all in that group.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="selected.length + ' selected'"></span>
            </div>
        </div>

        @error('rbac')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('rbac.*')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror

        {{-- Hidden inputs for selected permissions --}}
        <template x-for="perm in selected" :key="perm">
            <input type="hidden" name="rbac[]" :value="perm">
        </template>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white w-48">Module</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-900 dark:text-white w-20">
                                <button type="button" @click="toggle('*')" class="inline-flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors" :class="selected.includes('*') ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300 dark:border-gray-600'">
                                        <svg x-show="selected.includes('*')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    All
                                </button>
                            </th>
                            @foreach ($crudLabels as $action => $label)
                                <th class="text-center px-3 py-3 font-semibold text-gray-900 dark:text-white w-20">{{ $label }}</th>
                            @endforeach
                            <th class="text-center px-3 py-3 font-semibold text-gray-900 dark:text-white">Other</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($permissionGroups as $group => $permissions)
                            @php
                                $groupKeys = array_keys($permissions);
                                $crudPerms = [];
                                $otherPerms = [];
                                foreach ($permissions as $key => $label) {
                                    $action = substr($key, strrpos($key, '.') + 1);
                                    if (in_array($action, $crudActions) && $key !== '*') {
                                        $crudPerms[$action] = $key;
                                    } else {
                                        $otherPerms[$key] = $label;
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                {{-- Group Name --}}
                                <td class="px-4 py-3">
                                    <button type="button" @click="toggleGroup({{ json_encode($groupKeys) }})" class="text-left">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $group }}</span>
                                        <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-0.5" x-text="groupCount({{ json_encode($groupKeys) }}) + '/{{ count($groupKeys) }}'"></span>
                                    </button>
                                </td>

                                {{-- All toggle --}}
                                <td class="text-center px-3 py-3">
                                    <button type="button" @click="toggleGroup({{ json_encode($groupKeys) }})" class="inline-flex items-center justify-center">
                                        <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                            :class="{{ json_encode($groupKeys) }}.every(p => selected.includes(p)) ? 'bg-indigo-600 border-indigo-600' : ({{ json_encode($groupKeys) }}.some(p => selected.includes(p)) ? 'bg-indigo-200 border-indigo-400 dark:bg-indigo-800 dark:border-indigo-600' : 'border-gray-300 dark:border-gray-600')">
                                            <svg x-show="{{ json_encode($groupKeys) }}.every(p => selected.includes(p))" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <svg x-show="!{{ json_encode($groupKeys) }}.every(p => selected.includes(p)) && {{ json_encode($groupKeys) }}.some(p => selected.includes(p))" class="w-2.5 h-2.5 text-indigo-600 dark:text-indigo-300" fill="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
                                        </span>
                                    </button>
                                </td>

                                {{-- CRUD columns --}}
                                @foreach ($crudActions as $action)
                                    <td class="text-center px-3 py-3">
                                        @if (isset($crudPerms[$action]))
                                            <button type="button" @click="toggle('{{ $crudPerms[$action] }}')" class="inline-flex items-center justify-center">
                                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                                    :class="has('{{ $crudPerms[$action] }}') ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300 dark:border-gray-600'">
                                                    <svg x-show="has('{{ $crudPerms[$action] }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            </button>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Other permissions --}}
                                <td class="px-3 py-3">
                                    @if (!empty($otherPerms))
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            @foreach ($otherPerms as $key => $label)
                                                <button type="button" @click="toggle('{{ $key }}')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors"
                                                    :class="has('{{ $key }}') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 ring-1 ring-indigo-200 dark:ring-indigo-800' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'">
                                                    <svg x-show="has('{{ $key }}')" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('master-data.roles') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</a>
        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">{{ $submitLabel }}</button>
    </div>
</div>
