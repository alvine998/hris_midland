<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ChatRoom;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeNotification;
use App\Models\Facility;
use App\Models\FacilityCriteria;
use App\Models\LeaveSetting;
use App\Models\LoginAttempt;
use App\Models\Module;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Section;
use App\Models\Transfer;
use App\Models\TransferType;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\ListSearchService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCrudController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        $config = $this->config($resource);
        $model = $config['model'];
        $query = $model::query();

        foreach ($config['with'] ?? [] as $relation) {
            $query->with($relation);
        }

        $items = ListSearchService::apply($query, $request, $config['search'] ?? ['name', 'title', 'status'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin-crud.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $items,
            'options' => $this->options($config),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);
        $data = $this->validated($request, $config);
        $config['model']::create($data);

        return back()->with('success', $config['singular'].' created successfully.');
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $item = $config['model']::findOrFail($id);
        $item->update($this->validated($request, $config));

        return back()->with('success', $config['singular'].' updated successfully.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $config['model']::findOrFail($id)->delete();

        return back()->with('success', $config['singular'].' deleted successfully.');
    }

    private function config(string $resource): array
    {
        $configs = [
            'leave-settings' => [
                'title' => 'Leave Settings',
                'singular' => 'Leave setting',
                'model' => LeaveSetting::class,
                'with' => ['company'],
                'search' => [],
                'columns' => ['company.name' => 'Company', 'is_advance_leave' => 'Advance', 'max_advance_leave' => 'Max Advance', 'is_rollover' => 'Rollover', 'max_rollover' => 'Max Rollover'],
                'fields' => [
                    ['name' => 'company_id', 'label' => 'Company', 'type' => 'select', 'options' => 'companies'],
                    ['name' => 'is_advance_leave', 'label' => 'Advance Leave', 'type' => 'checkbox'],
                    ['name' => 'max_advance_leave', 'label' => 'Max Advance Leave', 'type' => 'number'],
                    ['name' => 'is_rollover', 'label' => 'Rollover', 'type' => 'checkbox'],
                    ['name' => 'max_rollover', 'label' => 'Max Rollover', 'type' => 'number'],
                ],
                'rules' => ['company_id' => ['nullable', 'exists:companies,id'], 'is_advance_leave' => ['boolean'], 'max_advance_leave' => ['nullable', 'integer', 'min:0'], 'is_rollover' => ['boolean'], 'max_rollover' => ['nullable', 'integer', 'min:0']],
            ],
            'activity-logs' => [
                'title' => 'Activity Logs',
                'singular' => 'Activity log',
                'model' => ActivityLog::class,
                'with' => ['user', 'module'],
                'search' => ['action'],
                'columns' => ['action' => 'Action', 'user.name' => 'User', 'module.name' => 'Module'],
                'fields' => [
                    ['name' => 'action', 'label' => 'Action', 'type' => 'textarea'],
                    ['name' => 'user_id', 'label' => 'User', 'type' => 'select', 'options' => 'users'],
                    ['name' => 'module_id', 'label' => 'Module', 'type' => 'select', 'options' => 'modules'],
                    ['name' => 'old_data', 'label' => 'Old Data JSON', 'type' => 'json'],
                    ['name' => 'new_data', 'label' => 'New Data JSON', 'type' => 'json'],
                ],
                'rules' => ['action' => ['required', 'string', 'max:2000'], 'user_id' => ['nullable', 'exists:users,id'], 'module_id' => ['nullable', 'exists:modules,id'], 'old_data' => ['nullable', 'array'], 'new_data' => ['nullable', 'array']],
            ],
            'login-attempts' => [
                'title' => 'Login Attempts',
                'singular' => 'Login attempt',
                'model' => LoginAttempt::class,
                'with' => ['user'],
                'search' => [],
                'columns' => ['user.name' => 'User', 'success' => 'Success'],
                'fields' => [
                    ['name' => 'user_id', 'label' => 'User', 'type' => 'select', 'options' => 'users'],
                    ['name' => 'success', 'label' => 'Success', 'type' => 'checkbox'],
                ],
                'rules' => ['user_id' => ['nullable', 'exists:users,id'], 'success' => ['boolean']],
            ],
            'facility-criterias' => $this->simpleConfig('Facility Criteria', FacilityCriteria::class),
            'facilities' => [
                'title' => 'Facilities',
                'singular' => 'Facility',
                'model' => Facility::class,
                'search' => ['name', 'description'],
                'columns' => ['name' => 'Name', 'facility_criteria_ids' => 'Criteria IDs', 'description' => 'Description'],
                'fields' => [
                    ['name' => 'facility_criteria_ids', 'label' => 'Criteria', 'type' => 'multiselect', 'options' => 'facilityCriterias'],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ],
                'rules' => ['facility_criteria_ids' => ['nullable', 'array'], 'facility_criteria_ids.*' => ['exists:facility_criterias,id'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:1000']],
            ],
            'payroll-periods' => [
                'title' => 'Payroll Periods',
                'singular' => 'Payroll period',
                'model' => PayrollPeriod::class,
                'with' => ['company'],
                'search' => ['status'],
                'columns' => ['company.name' => 'Company', 'month' => 'Month', 'year' => 'Year', 'status' => 'Status'],
                'fields' => [
                    ['name' => 'company_id', 'label' => 'Company', 'type' => 'select', 'options' => 'companies'],
                    ['name' => 'month', 'label' => 'Month', 'type' => 'number'],
                    ['name' => 'year', 'label' => 'Year', 'type' => 'number'],
                    ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
                    ['name' => 'end_date', 'label' => 'End Date', 'type' => 'date'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
                ],
                'rules' => ['company_id' => ['nullable', 'exists:companies,id'], 'month' => ['required', 'integer', 'between:1,12'], 'year' => ['required', 'integer', 'digits:4'], 'start_date' => ['nullable', 'date'], 'end_date' => ['nullable', 'date', 'after_or_equal:start_date'], 'status' => ['nullable', 'string', 'max:50']],
            ],
            'payrolls' => [
                'title' => 'Payrolls',
                'singular' => 'Payroll',
                'model' => Payroll::class,
                'with' => ['payrollPeriod', 'employee'],
                'search' => ['status'],
                'columns' => ['employee.name' => 'Employee', 'payrollPeriod.year' => 'Year', 'basic_salary' => 'Basic Salary', 'take_home_pay' => 'Take Home Pay', 'status' => 'Status'],
                'fields' => [
                    ['name' => 'payroll_period_id', 'label' => 'Payroll Period', 'type' => 'select', 'options' => 'payrollPeriods'],
                    ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'options' => 'employees'],
                    ['name' => 'basic_salary', 'label' => 'Basic Salary', 'type' => 'number'],
                    ['name' => 'allowance_total', 'label' => 'Allowance Total', 'type' => 'number'],
                    ['name' => 'deduction_total', 'label' => 'Deduction Total', 'type' => 'number'],
                    ['name' => 'bpjs_total', 'label' => 'BPJS Total', 'type' => 'number'],
                    ['name' => 'tax_pph21', 'label' => 'Tax PPH21', 'type' => 'number'],
                    ['name' => 'take_home_pay', 'label' => 'Take Home Pay', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select_static', 'choices' => ['paid' => 'Paid', 'unpaid' => 'Unpaid']],
                ],
                'rules' => ['payroll_period_id' => ['nullable', 'exists:payroll_periods,id'], 'employee_id' => ['nullable', 'exists:employees,id'], 'basic_salary' => ['required', 'integer', 'min:0'], 'allowance_total' => ['required', 'integer', 'min:0'], 'deduction_total' => ['required', 'integer', 'min:0'], 'bpjs_total' => ['required', 'integer', 'min:0'], 'tax_pph21' => ['required', 'integer', 'min:0'], 'take_home_pay' => ['required', 'integer', 'min:0'], 'status' => ['required', 'in:paid,unpaid']],
            ],
            'transfer-types' => $this->simpleConfig('Transfer Types', TransferType::class, true),
            'transfers' => [
                'title' => 'Transfers',
                'singular' => 'Transfer',
                'model' => Transfer::class,
                'with' => ['employee', 'transferType'],
                'search' => ['reason', 'status'],
                'columns' => ['employee.name' => 'Employee', 'transferType.name' => 'Type', 'transfer_from' => 'From', 'transfer_to' => 'To', 'status' => 'Status'],
                'fields' => [
                    ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'options' => 'employees'],
                    ['name' => 'transfer_type_id', 'label' => 'Transfer Type', 'type' => 'select', 'options' => 'transferTypes'],
                    ['name' => 'reason', 'label' => 'Reason', 'type' => 'textarea'],
                    ['name' => 'transfer_from', 'label' => 'Transfer From ID', 'type' => 'number'],
                    ['name' => 'transfer_to', 'label' => 'Transfer To ID', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
                ],
                'rules' => ['employee_id' => ['nullable', 'exists:employees,id'], 'transfer_type_id' => ['nullable', 'exists:transfer_types,id'], 'reason' => ['nullable', 'string', 'max:2000'], 'transfer_from' => ['nullable', 'integer'], 'transfer_to' => ['nullable', 'integer'], 'status' => ['nullable', 'string', 'max:50']],
            ],
            'notifications' => [
                'title' => 'Notifications',
                'singular' => 'Notification',
                'model' => EmployeeNotification::class,
                'with' => ['company'],
                'search' => ['title', 'message', 'status'],
                'columns' => ['title' => 'Title', 'company.name' => 'Company', 'status' => 'Status', 'is_read' => 'Read'],
                'fields' => [
                    ['name' => 'company_id', 'label' => 'Company', 'type' => 'select', 'options' => 'companies'],
                    ['name' => 'department_id', 'label' => 'Department', 'type' => 'select', 'options' => 'departments'],
                    ['name' => 'division_id', 'label' => 'Division', 'type' => 'select', 'options' => 'divisions'],
                    ['name' => 'section_id', 'label' => 'Section', 'type' => 'select', 'options' => 'sections'],
                    ['name' => 'work_location_id', 'label' => 'Work Location', 'type' => 'select', 'options' => 'workLocations'],
                    ['name' => 'user_ids', 'label' => 'Personal Users', 'type' => 'multiselect', 'options' => 'users'],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea'],
                    ['name' => 'file', 'label' => 'File', 'type' => 'text'],
                    ['name' => 'is_read', 'label' => 'Read', 'type' => 'checkbox'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select_static', 'choices' => ['sent' => 'Sent', 'pending' => 'Pending', 'draft' => 'Draft']],
                ],
                'rules' => ['company_id' => ['nullable', 'exists:companies,id'], 'department_id' => ['nullable', 'exists:departments,id'], 'division_id' => ['nullable', 'exists:divisions,id'], 'section_id' => ['nullable', 'exists:sections,id'], 'work_location_id' => ['nullable', 'exists:work_locations,id'], 'user_ids' => ['nullable', 'array'], 'user_ids.*' => ['exists:users,id'], 'title' => ['required', 'string', 'max:255'], 'message' => ['nullable', 'string', 'max:2000'], 'file' => ['nullable', 'string', 'max:2000'], 'is_read' => ['boolean'], 'status' => ['required', 'in:sent,pending,draft']],
            ],
            'chat-rooms' => [
                'title' => 'Chat Rooms',
                'singular' => 'Chat room',
                'model' => ChatRoom::class,
                'search' => [],
                'columns' => ['id' => 'ID', 'messages' => 'Messages'],
                'fields' => [['name' => 'messages', 'label' => 'Messages JSON', 'type' => 'json']],
                'rules' => ['messages' => ['nullable', 'array']],
            ],
        ];

        abort_unless(isset($configs[$resource]), 404);

        return $configs[$resource];
    }

    private function simpleConfig(string $title, string $model, bool $description = false): array
    {
        $fields = [['name' => 'name', 'label' => 'Name', 'type' => 'text']];
        $rules = ['name' => ['required', 'string', 'max:255']];
        $columns = ['name' => 'Name'];

        if ($description) {
            $fields[] = ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'];
            $rules['description'] = ['nullable', 'string', 'max:1000'];
            $columns['description'] = 'Description';
        }

        return [
            'title' => $title,
            'singular' => Str::singular($title),
            'model' => $model,
            'search' => array_keys($columns),
            'columns' => $columns,
            'fields' => $fields,
            'rules' => $rules,
        ];
    }

    private function options(array $config): array
    {
        $needed = collect($config['fields'])->pluck('options')->filter()->unique();
        $options = [];

        foreach ($needed as $key) {
            $options[$key] = match ($key) {
                'companies' => Company::orderBy('name')->get(['id', 'name']),
                'users' => User::orderBy('name')->get(['id', 'name']),
                'modules' => Module::orderBy('name')->get(['id', 'name']),
                'employees' => Employee::orderBy('name')->get(['id', 'name']),
                'departments' => Department::orderBy('name')->get(['id', 'name']),
                'divisions' => Division::orderBy('name')->get(['id', 'name']),
                'sections' => Section::orderBy('name')->get(['id', 'name']),
                'workLocations' => WorkLocation::orderBy('name')->get(['id', 'name']),
                'facilityCriterias' => FacilityCriteria::orderBy('name')->get(['id', 'name']),
                'payrollPeriods' => PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get()->map(fn ($period) => (object) ['id' => $period->id, 'name' => $period->month.'/'.$period->year]),
                'transferTypes' => TransferType::orderBy('name')->get(['id', 'name']),
                default => collect(),
            };
        }

        return $options;
    }

    private function validated(Request $request, array $config): array
    {
        foreach ($config['fields'] as $field) {
            if (($field['type'] ?? null) === 'json' && $request->filled($field['name'])) {
                $decoded = json_decode($request->input($field['name']), true);
                $request->merge([$field['name'] => is_array($decoded) ? $decoded : []]);
            }
        }

        $data = $request->validate($config['rules']);

        foreach ($config['fields'] as $field) {
            if (($field['type'] ?? null) === 'checkbox') {
                $data[$field['name']] = $request->boolean($field['name']);
            }
        }

        return $data;
    }

    public static function value(Model $item, string $key): mixed
    {
        return data_get($item, $key);
    }

    public static function jsItem(Model $item, array $fields): array
    {
        return collect($fields)->mapWithKeys(function ($field) use ($item): array {
            $value = $item->{$field['name']} ?? null;

            if (($field['type'] ?? null) === 'date' && $value) {
                $value = $value->format('Y-m-d');
            }

            if (($field['type'] ?? null) === 'json') {
                $value = json_encode($value ?? [], JSON_PRETTY_PRINT);
            }

            return [$field['name'] => $value];
        })->merge(['id' => $item->id])->all();
    }
}
