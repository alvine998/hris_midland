<?php

namespace Database\Seeders;

use App\Models\AiChatKnowledge;
use Illuminate\Database\Seeder;

class AiChatKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'category' => 'Leave Policy',
                'title' => 'Annual Leave Entitlement',
                'content' => 'Full-time employees are entitled to 12 days of annual leave per year. Annual leave accrues monthly at a rate of 1 day per month of continuous service. New employees may use accrued leave after completing their probation period (3 months). Annual leave must be requested at least 3 days in advance and approved by the direct supervisor. Unused annual leave may be carried over to the next year, but a maximum of 5 days can be carried over. Employees are encouraged to use their annual leave within the calendar year.',
            ],
            [
                'category' => 'Leave Policy',
                'title' => 'Sick Leave Policy',
                'content' => 'Employees are entitled to paid sick leave of up to 12 days per year. A medical certificate is required for absences of 2 or more consecutive days. Sick leave cannot be carried over to the next year. For extended illness beyond 12 days, employees may apply for unpaid medical leave with supporting medical documentation. Self-certification is allowed for single-day absences.',
            ],
            [
                'category' => 'Leave Policy',
                'title' => 'Maternity and Paternity Leave',
                'content' => 'Female employees are entitled to 90 days of paid maternity leave. Male employees are entitled to 10 days of paid paternity leave. Maternity leave may begin up to 30 days before the expected delivery date. Both parents must notify HR at least 30 days before the expected leave start date.',
            ],
            [
                'category' => 'Attendance',
                'title' => 'Working Hours and Attendance',
                'content' => 'Standard working hours are Monday to Friday, 08:00 to 17:00, with a 1-hour lunch break. Employees must clock in and out using the attendance system. Tardiness of more than 15 minutes without prior approval will be recorded as a late mark. Three late marks in one month will result in a written warning. Absence without prior approval for 3 or more consecutive working days will be considered job abandonment unless a valid reason is provided.',
            ],
            [
                'category' => 'Attendance',
                'title' => 'Overtime Policy',
                'content' => 'Overtime work must be pre-approved by the direct supervisor and documented in the system. Overtime compensation is provided at 1.5x the regular hourly rate for weekdays, 2x for Saturdays, and 3x for Sundays and public holidays. Overtime hours are capped at 36 hours per month per employee. Emergency overtime may be approved retroactively within 3 working days.',
            ],
            [
                'category' => 'Payroll',
                'title' => 'Salary Payment Schedule',
                'content' => 'Salaries are paid on the last working day of each month via bank transfer. Payslips are available in the employee portal. Salary deductions include statutory contributions (BPJS Kesehatan, BPJS Ketenagakerjaan), income tax, and any approved personal deductions. Employees must update their bank account details with HR within 5 working days of any change.',
            ],
            [
                'category' => 'Performance',
                'title' => 'Performance Review Process',
                'content' => 'Performance reviews are conducted semi-annually (June and December). Reviews consist of self-assessment, supervisor assessment, and 360-degree feedback. Employees must complete their self-assessment within the given timeline. The review period covers KPIs, competency assessment, and behavioral evaluation. Performance ratings range from 1 (Needs Improvement) to 5 (Outstanding). Employees rated 4 or above are eligible for promotion consideration.',
            ],
            [
                'category' => 'Code of Conduct',
                'title' => 'Workplace Ethics and Conduct',
                'content' => 'All employees must maintain professional conduct in the workplace. Harassment, discrimination, and bullying are strictly prohibited. Violations of the code of conduct may result in warnings, suspension, or termination depending on severity. Employees must report any violations to HR or through the anonymous reporting channel. Confidential company information must not be shared with unauthorized parties.',
            ],
        ];

        foreach ($articles as $article) {
            AiChatKnowledge::updateOrCreate(
                ['title' => $article['title']],
                ['category' => $article['category'], 'content' => $article['content'], 'is_active' => true],
            );
        }
    }
}
