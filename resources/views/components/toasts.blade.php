@php
    $toasts = [];

    foreach (['success', 'status'] as $key) {
        if (session()->has($key)) {
            $toasts[] = ['type' => 'success', 'message' => session($key)];
        }
    }

    foreach (['warning', 'error'] as $key) {
        if (session()->has($key)) {
            $toasts[] = ['type' => $key, 'message' => session($key)];
        }
    }

    if ($errors->any() && ! session()->has('error') && ! session()->has('warning')) {
        $toasts[] = ['type' => 'error', 'message' => $errors->first()];
    }
@endphp

@if ($toasts)
    <div
        x-data="{
            toasts: @js($toasts),
            remove(index) {
                this.toasts.splice(index, 1);
            },
            styles(type) {
                return {
                    success: 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/40 dark:text-green-200',
                    warning: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                    error: 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-200',
                }[type] || 'border-gray-200 bg-white text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200';
            },
            labels(type) {
                return {
                    success: 'Success',
                    warning: 'Warning',
                    error: 'Error',
                }[type] || 'Info';
            },
            init() {
                this.toasts.forEach((toast, index) => {
                    setTimeout(() => this.remove(index), 5000 + (index * 600));
                });
            },
        }"
        class="fixed right-4 top-4 z-[100] w-[calc(100%-2rem)] max-w-sm space-y-3"
        aria-live="polite"
    >
        <template x-for="(toast, index) in toasts" :key="`${toast.type}-${index}-${toast.message}`">
            <div
                x-transition
                class="rounded-xl border p-4 shadow-lg"
                :class="styles(toast.type)"
            >
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold" x-text="labels(toast.type)"></p>
                        <p class="mt-1 text-sm leading-5" x-text="toast.message"></p>
                    </div>
                    <button type="button" class="shrink-0 text-lg leading-none opacity-70 hover:opacity-100" @click="remove(index)" aria-label="Close notification">
                        &times;
                    </button>
                </div>
            </div>
        </template>
    </div>
@endif
