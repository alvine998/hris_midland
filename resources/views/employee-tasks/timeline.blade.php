@extends('layouts.app')

@section('title', 'Task Timeline - ' . config('app.name'))

@section('content')
@php
    $previousMonth = $month === 1 ? 12 : $month - 1;
    $previousYear = $month === 1 ? $year - 1 : $year;
    $nextMonth = $month === 12 ? 1 : $month + 1;
    $nextYear = $month === 12 ? $year + 1 : $year;
    $statusDot = [
        'pending' => 'bg-amber-500',
        'in_progress' => 'bg-blue-500',
        'completed' => 'bg-emerald-500',
        'cancelled' => 'bg-red-500',
    ];
    $statusPill = [
        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
        'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
        'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
        'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-300',
    ];
@endphp

<div
    x-data="{
        taskModal: false,
        dayModal: false,
        selectedTask: null,
        selectedDayLabel: '',
        selectedDayTasks: [],
        formatDate(iso){
            if(!iso) return '-';
            const datePart = String(iso).split('T')[0];
            const p = datePart.split('-');
            if(p.length===3) return `${p[2]}-${p[1]}-${p[0]}`;
            return iso;
        },
        openTaskModal(task) {
            this.selectedTask = task;
            this.taskModal = true;
        },
        openDayModal(label, tasksJson) {
            this.selectedDayLabel = label;
            try { this.selectedDayTasks = typeof tasksJson === 'string' ? JSON.parse(tasksJson) : tasksJson; } catch(e){ this.selectedDayTasks = tasksJson; }
            this.dayModal = true;
        },
        closeDayAndOpenTask(task){ this.dayModal=false; this.$nextTick(()=>this.openTaskModal(task)); }
    }"
    class="space-y-6"
>
    {{-- Page header --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Task Timeline</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Monthly calendar — click any task to view details.</p>
        </div>
        <a href="{{ route('employee-tasks.index') }}" class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">List View</a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('employee-tasks.timeline') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Month</label>
            <select name="month" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                @foreach (range(1,12) as $m)
                    <option value="{{ $m }}" @selected($month===$m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Year</label>
            <select name="year" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                @foreach (range(now()->year-2, now()->year+2) as $y)
                    <option value="{{ $y }}" @selected($year===$y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        @if ($canAssign)
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Employee</label>
                <select name="employee_id" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    <option value="">All employees</option>
                    @foreach ($employees as $e)
                        <option value="{{ $e->id }}" @selected((string)request('employee_id')===(string)$e->id)>{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
        <a href="{{ route('employee-tasks.timeline') }}" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300">Reset</a>
    </form>

    {{-- Calendar navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('employee-tasks.timeline', ['month'=>$previousMonth,'year'=>$previousYear,'employee_id'=>request('employee_id')]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ \Carbon\Carbon::create($previousYear,$previousMonth,1)->format('M Y') }}
        </a>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $startOfMonth->format('F Y') }}</h3>
        <a href="{{ route('employee-tasks.timeline', ['month'=>$nextMonth,'year'=>$nextYear,'employee_id'=>request('employee_id')]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ \Carbon\Carbon::create($nextYear,$nextMonth,1)->format('M Y') }}
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Calendar — exact pipe table design: SUN | MON | TUE | WED | THU | FRI | SAT --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900">
                        @foreach (['SUN','MON','TUE','WED','THU','FRI','SAT'] as $i => $d)
                            <th class="border border-gray-200 px-2 py-3 text-center text-xs font-bold tracking-widest dark:border-gray-700 {{ $i===0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300' }}">{{ $d }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_chunk($calendarDays, 7) as $week)
                        <tr>
                            @foreach ($week as $day)
                                @php
                                    $isCurrentMonth = $day->month === $month;
                                    $isToday = $day->isToday();
                                    $dayTasks = $tasks->filter(fn($t) => $day->between($t->period_start->copy()->startOfDay(), $t->period_end->copy()->endOfDay()))->sortBy(fn($t) => ['urgent'=>0,'high'=>1,'normal'=>2,'low'=>3][$t->priority] ?? 2);
                                    $visible = $dayTasks->take(3);
                                    $overflow = $dayTasks->count() - $visible->count();
                                    $dayLabel = $day->format('l, F j, Y');
                                    $payload = $dayTasks->map(fn($t) => ['id'=>$t->id,'title'=>$t->title,'description'=>$t->description,'period_type'=>$t->period_type,'period_start'=>$t->period_start->format('Y-m-d'),'period_end'=>$t->period_end->format('Y-m-d'),'priority'=>$t->priority,'status'=>$t->status,'employee'=>$t->employee?['name'=>$t->employee->name,'nip'=>$t->employee->nip,'department'=>$t->employee->department?['name'=>$t->employee->department->name]:null]:null,'evidence_files'=>$t->evidence_files])->values()->toJson();
                                @endphp
                                <td class="h-[118px] border border-gray-200 align-top dark:border-gray-700 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900/40' }} {{ $isToday ? 'bg-indigo-50/40 dark:bg-indigo-900/10' : '' }} p-1.5 sm:h-[132px] sm:p-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold sm:h-7 sm:w-7 sm:text-sm {{ $isToday ? 'bg-indigo-600 text-white' : ($isCurrentMonth ? 'text-gray-900 dark:text-white' : 'text-gray-400') }}">{{ $day->format('j') }}</span>
                                        @if($dayTasks->count() > 0)
                                            <span class="hidden text-[10px] font-bold text-gray-400 dark:text-gray-500 sm:block">{{ $dayTasks->count() }} task{{ $dayTasks->count()>1?'s':'' }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1.5 space-y-1">
                                        @foreach ($visible as $task)
                                            <button type="button" @click="openTaskModal({{ $task->toJson() }})" class="flex w-full items-center gap-1.5 truncate rounded-md px-2 py-1 text-left text-[11px] font-medium leading-none ring-1 ring-black/5 dark:ring-white/10 {{ $statusPill[$task->status] ?? $statusPill['pending'] }}">
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $statusDot[$task->status] ?? 'bg-gray-400' }}"></span>
                                                <span class="min-w-0 flex-1 truncate">{{ Str::limit($task->title, 18) }}</span>
                                            </button>
                                        @endforeach
                                        @if($overflow > 0)
                                            <button type="button" @click="openDayModal(@js($dayLabel), @js($payload))" class="w-full rounded-md bg-gray-900 px-2 py-1 text-center text-[11px] font-bold text-white hover:bg-black dark:bg-white dark:text-gray-900">+{{ $overflow }} more</button>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Task modal --}}
    <div x-show="taskModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="taskModal=false"></div>
        <div class="relative flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-800">
            <div class="border-b px-6 py-4 dark:border-gray-700">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="selectedTask?.title"></h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="selectedTask ? formatDate(selectedTask.period_start) + ' → ' + formatDate(selectedTask.period_end) + ' · ' + selectedTask.priority : ''"></p>
                    </div>
                    <button @click="taskModal=false" class="rounded-full p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6 text-sm">
                <p class="whitespace-pre-wrap text-gray-600 dark:text-gray-300" x-text="selectedTask?.description || 'No description'"></p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Start Date</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedTask?.period_start)"></p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">End Date</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedTask?.period_end)"></p>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</p>
                        <p class="mt-1"><span class="rounded-full px-2 py-0.5 text-xs font-bold ring-1" :class="{'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300': selectedTask?.status==='pending','bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300': selectedTask?.status==='in_progress','bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300': selectedTask?.status==='completed','bg-red-50 text-red-700 ring-red-200 dark:bg-red-500/10 dark:text-red-300': selectedTask?.status==='cancelled'}" x-text="selectedTask?.status?.replace('_',' ')"></span></p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Employee</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white" x-text="selectedTask?.employee?.name || '-'"></p>
                        <p class="text-xs text-gray-500" x-text="selectedTask?.employee?.department?.name || ''"></p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 border-t bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                <button @click="taskModal=false" class="flex-1 rounded-xl border bg-white py-2.5 text-sm font-semibold dark:border-gray-600 dark:bg-gray-800 dark:text-white">Close</button>
                <a :href="`/employee-tasks/${selectedTask?.id}/edit`" class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-700">Edit</a>
            </div>
        </div>
    </div>

    {{-- Day modal --}}
    <div x-show="dayModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="dayModal=false"></div>
        <div class="relative flex max-h-[80vh] w-full max-w-md flex-col overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-800">
            <div class="border-b px-6 py-4 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white" x-text="selectedDayLabel"></h3>
                <p class="text-xs text-gray-500" x-text="(selectedDayTasks?.length||0)+' tasks'"></p>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <template x-for="t in selectedDayTasks" :key="t.id">
                    <button @click="closeDayAndOpenTask(t)" class="flex w-full items-center gap-3 rounded-xl border p-3 text-left hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                        <span class="h-8 w-1 rounded-full" :class="{'bg-red-500':t.priority==='urgent','bg-amber-500':t.priority==='high','bg-indigo-500':t.priority==='normal','bg-gray-300':t.priority==='low'}"></span>
                        <span class="flex-1 min-w-0"><span class="block truncate text-sm font-semibold dark:text-white" x-text="t.title"></span><span class="block truncate text-xs text-gray-500" x-text="t.employee?.name || 'Unassigned'"></span></span>
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="{'bg-amber-50 text-amber-700':t.status==='pending','bg-blue-50 text-blue-700':t.status==='in_progress','bg-emerald-50 text-emerald-700':t.status==='completed','bg-red-50 text-red-700':t.status==='cancelled'}" x-text="t.status"></span>
                    </button>
                </template>
            </div>
            <div class="border-t p-4 dark:border-gray-700"><button @click="dayModal=false" class="w-full rounded-xl bg-gray-900 py-2.5 text-sm font-semibold text-white dark:bg-white dark:text-gray-900">Close</button></div>
        </div>
    </div>
</div>
@endsection
