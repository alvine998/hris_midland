<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\Holiday;
use App\Models\Kpi;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Ai\FunctionDispatcher;
use App\Services\Ai\FunctionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AiAdminFunctionsTest extends TestCase
{
    use RefreshDatabase;

    protected FunctionRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = app(FunctionRegistry::class);
    }

    public function test_admin_user_can_dispatch_admin_function(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('John Doe', 'john@example.test');

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_employee_detail', ['search' => 'John'], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals('John Doe', $result['data']['employee']['name']);
    }

    public function test_non_admin_user_cannot_dispatch_admin_function(): void
    {
        $regularUser = $this->createRegularUser();
        $this->createEmployee('Jane Doe', 'jane@example.test');

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_dashboard_summary', [], $regularUser);

        $this->assertFalse($result['success']);
        $this->assertEquals('Permission denied', $result['error']);
    }

    public function test_admin_can_get_all_leave_requests(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('Leave Employee', 'leave@example.test');
        $leaveType = LeaveType::create(['name' => 'Annual', 'max_days' => 12]);
        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'title' => 'Vacation',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'inclusive_days' => 3,
            'status' => 'pending',
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_all_leave_requests', ['status' => 'pending'], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['count']);
        $this->assertEquals('Vacation', $result['data']['leave_requests'][0]['title']);
    }

    public function test_admin_can_get_dashboard_summary(): void
    {
        $admin = $this->createAdmin();
        $this->createEmployee('Active Emp', 'active@example.test');
        $company = $this->createCompany();
        Department::create(['name' => 'Engineering', 'company_id' => $company->id]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_dashboard_summary', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['data']['metrics']['total_employees']);
    }

    public function test_admin_can_get_all_tasks(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('Task Employee', 'task@example.test');
        EmployeeTask::create([
            'employee_id' => $employee->id,
            'title' => 'Quarterly review',
            'period_type' => 'monthly',
            'period_start' => '2026-07-01',
            'period_end' => '2026-07-31',
            'status' => 'pending',
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_all_tasks', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['count']);
    }

    public function test_admin_can_get_employee_attendance(): void
    {
        Carbon::setTestNow('2026-07-08');
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('Attend Emp', 'attend@example.test');
        Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => '2026-07-08 08:00:00',
            'status' => 'present',
            'work_hours' => 8,
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_employee_attendance', ['employee' => 'Attend'], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals('Attend Emp', $result['data']['employee']);
        Carbon::setTestNow();
    }

    public function test_admin_can_get_payrolls(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('Payroll Emp', 'pay@example.test');
        $period = PayrollPeriod::create([
            'month' => 7,
            'year' => 2026,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
            'status' => 'open',
        ]);
        Payroll::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'basic_salary' => 5000000,
            'take_home_pay' => 4500000,
            'status' => 'draft',
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_payrolls', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['count']);
    }

    public function test_admin_can_get_holidays(): void
    {
        Carbon::setTestNow('2026-07-08');
        $admin = $this->createAdmin();
        Holiday::create([
            'name' => 'Independence Day',
            'start_date' => '2026-08-17',
            'end_date' => '2026-08-17',
            'type' => 'national',
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_holidays', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['count']);
        Carbon::setTestNow();
    }

    public function test_admin_can_get_kpis(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee('Kpi Emp', 'kpi@example.test');
        Kpi::create([
            'employee_id' => $employee->id,
            'title' => 'Sales target',
            'period' => 'Q3 2026',
            'target' => 100,
            'actual' => 80,
            'weight' => 50,
            'score' => 80,
            'status' => 'active',
        ]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_kpis', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['count']);
    }

    public function test_admin_can_get_departments(): void
    {
        $admin = $this->createAdmin();
        $company = $this->createCompany();
        Department::create(['name' => 'HR', 'company_id' => $company->id]);
        Department::create(['name' => 'Finance', 'company_id' => $company->id]);

        $dispatcher = new FunctionDispatcher($this->registry);
        $result = $dispatcher->dispatch('get_departments', [], $admin);

        $this->assertTrue($result['success']);
        $this->assertGreaterThanOrEqual(2, $result['data']['count']);
    }

    public function test_registry_registers_all_admin_functions(): void
    {
        $all = $this->registry->getAllFunctions();

        $expectedFunctions = [
            'get_employee_detail',
            'get_all_leave_requests',
            'get_employee_attendance',
            'get_payrolls',
            'get_payroll_periods',
            'get_departments',
            'get_holidays',
            'get_kpis',
            'get_dashboard_summary',
            'get_pending_leave_approvals',
            'get_all_tasks',
            'get_transfers',
            'get_activity_logs',
            'get_companies',
        ];

        foreach ($expectedFunctions as $name) {
            $this->assertArrayHasKey($name, $all, "Function '{$name}' should be registered.");
        }
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create(['email' => 'admin@example.test']);
        $role = Role::create(['name' => 'Admin', 'description' => 'Full access', 'rbac' => ['*']]);
        UserRole::create(['user_id' => $user->id, 'role_id' => $role->id]);

        return $user->refresh();
    }

    private function createRegularUser(): User
    {
        $user = User::factory()->create(['email' => 'regular@example.test']);
        $role = Role::create(['name' => 'Employee', 'description' => 'Basic access', 'rbac' => ['employee.view']]);
        UserRole::create(['user_id' => $user->id, 'role_id' => $role->id]);

        return $user->refresh();
    }

    private function createEmployee(string $name, string $email): Employee
    {
        return Employee::create([
            'name' => $name,
            'email' => $email,
            'phone' => '08123456789',
            'status' => 'active',
        ]);
    }

    private function createCompany(): Company
    {
        return Company::create([
            'name' => 'Test Company',
            'email' => 'company@example.test',
            'phone' => '08123456789',
            'address' => '123 Test Street',
            'status' => 'active',
        ]);
    }
}
