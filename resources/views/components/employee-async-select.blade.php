@props([
    'name',
    'model' => null,
    'multiple' => false,
    'placeholder' => 'Search employee...',
    'required' => false,
    'class' => '',
])

@php($isMultiple = filter_var($multiple, FILTER_VALIDATE_BOOL))

<div
    x-data="{
        endpoint: @js(route('employee-options.index')),
        multiple: @js($isMultiple),
        search: '',
        open: false,
        loading: false,
        options: [],
        selected: [],
        init() {
            this.syncFromModel();
            @if ($model)
                this.$watch('{{ $model }}', () => this.syncFromModel());
            @endif
        },
        get selectedIds() {
            return this.selected.map((option) => String(option.id));
        },
        currentModelValue() {
            @if ($model)
                return {{ $model }};
            @else
                return this.multiple ? this.selectedIds : (this.selected[0]?.id ?? null);
            @endif
        },
        normalizeIds(value) {
            if (Array.isArray(value)) {
                return value.filter(Boolean).map((id) => String(id));
            }

            return value ? [String(value)] : [];
        },
        syncFromModel() {
            const ids = this.normalizeIds(this.currentModelValue());

            if (ids.join(',') === this.selectedIds.join(',')) {
                return;
            }

            if (ids.length === 0) {
                this.selected = [];
                this.search = '';
                return;
            }

            this.loadSelected(ids);
        },
        syncModel() {
            @if ($model)
                {{ $model }} = this.multiple ? this.selectedIds : (this.selected[0]?.id ?? null);
            @endif
        },
        optionLabel(option) {
            return option.label;
        },
        async fetchOptions(params = {}) {
            const url = new URL(this.endpoint, window.location.origin);

            if (params.search) {
                url.searchParams.set('search', params.search);
            }

            (params.selected ?? []).forEach((id) => url.searchParams.append('selected[]', id));

            const response = await fetch(url, { headers: { Accept: 'application/json' } });

            if (! response.ok) {
                return [];
            }

            const data = await response.json();

            return data.results ?? [];
        },
        async loadSelected(ids) {
            const results = await this.fetchOptions({ selected: ids });
            this.selected = results.filter((option) => ids.includes(String(option.id)));

            if (! this.multiple) {
                this.search = this.selected[0]?.label ?? '';
            }
        },
        async searchEmployees() {
            this.loading = true;
            this.open = true;

            try {
                this.options = await this.fetchOptions({
                    search: this.search,
                    selected: this.selectedIds,
                });
            } finally {
                this.loading = false;
            }
        },
        select(option) {
            if (this.multiple) {
                if (! this.selectedIds.includes(String(option.id))) {
                    this.selected.push(option);
                }

                this.search = '';
                this.searchEmployees();
            } else {
                this.selected = [option];
                this.search = option.label;
                this.open = false;
            }

            this.syncModel();
        },
        remove(option) {
            this.selected = this.selected.filter((selectedOption) => String(selectedOption.id) !== String(option.id));
            this.syncModel();

            if (! this.multiple) {
                this.search = '';
            }
        },
    }"
    class="relative {{ $class }}"
>
    <template x-for="option in selected" :key="`employee-input-${option.id}`">
        <input type="hidden" name="{{ $name }}[]" :value="option.id" :disabled="! multiple">
    </template>

    <input type="hidden" name="{{ $name }}" :value="selected[0]?.id ?? ''" :disabled="multiple">

    <div class="space-y-2">
        <template x-if="multiple && selected.length">
            <div class="flex flex-wrap gap-2 rounded-lg border border-gray-200 bg-gray-50 p-2 dark:border-gray-600 dark:bg-gray-900">
                <template x-for="option in selected" :key="option.id">
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
                        <span x-text="option.label"></span>
                        <button type="button" @click="remove(option)" class="text-indigo-500 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-white" aria-label="Remove employee">&times;</button>
                    </span>
                </template>
            </div>
        </template>

        <div class="relative">
            <input
                type="search"
                x-model="search"
                @focus="searchEmployees()"
                @input.debounce.300ms="searchEmployees()"
                @keydown.escape="open = false"
                placeholder="{{ $placeholder }}"
                @if ($required) required @endif
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >

            <template x-if="! multiple && selected.length">
                <button type="button" @click="remove(selected[0])" class="absolute right-2 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    Clear
                </button>
            </template>
        </div>
    </div>

    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        class="absolute z-50 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
    >
        <div x-show="loading" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Searching...</div>

        <template x-if="! loading && options.length === 0">
            <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">No employees found.</div>
        </template>

        <template x-for="option in options" :key="option.id">
            <button
                type="button"
                @click="select(option)"
                class="flex w-full items-start justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="selectedIds.includes(String(option.id)) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-200'"
            >
                <span>
                    <span class="block font-medium" x-text="option.name"></span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400" x-text="option.meta"></span>
                </span>
                <span x-show="selectedIds.includes(String(option.id))" class="text-xs font-semibold">Selected</span>
            </button>
        </template>
    </div>
</div>
