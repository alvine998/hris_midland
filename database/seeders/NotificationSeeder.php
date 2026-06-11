<?php

namespace Database\Seeders;

use App\Models\EmployeeNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        EmployeeNotification::create([
            'company_id' => 1,
            'title' => 'New Employee Onboarding Completed',
            'message' => 'Budi Santoso has completed the onboarding process and is now active in the system.',
            'status' => 'sent',
            'is_read' => false,
            'created_at' => $now->copy()->subDays(4),
            'updated_at' => $now->copy()->subDays(4),
        ]);

        EmployeeNotification::create([
            'company_id' => 1,
            'user_ids' => [1],
            'title' => 'Leave Request Approved',
            'message' => 'Your leave request for Annual Leave (3 days) has been approved by your supervisor.',
            'status' => 'sent',
            'is_read' => false,
            'created_at' => $now->copy()->subDays(3),
            'updated_at' => $now->copy()->subDays(3),
        ]);

        EmployeeNotification::create([
            'company_id' => 1,
            'title' => 'Payroll Processing Reminder',
            'message' => 'Payroll for June 2026 will be processed on June 25th. Please ensure all timesheets are submitted by June 20th.',
            'status' => 'sent',
            'is_read' => false,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        EmployeeNotification::create([
            'company_id' => 1,
            'user_ids' => [2],
            'title' => 'Performance Review Scheduled',
            'message' => 'Your mid-year performance review is scheduled for next week. Please prepare your self-assessment.',
            'status' => 'sent',
            'is_read' => false,
            'created_at' => $now->copy()->subDay(),
            'updated_at' => $now->copy()->subDay(),
        ]);

        EmployeeNotification::create([
            'company_id' => 1,
            'title' => 'System Maintenance Notice',
            'message' => 'The HRIS system will be undergoing maintenance on Saturday, June 15, from 10 PM to 2 AM. The system may be temporarily unavailable.',
            'status' => 'sent',
            'is_read' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
