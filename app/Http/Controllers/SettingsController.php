<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings');
    }

    public function documentation(): View
    {
        return view('settings.documentation', [
            'modules' => [
                [
                    'title' => 'Dashboard',
                    'description' => 'Use the dashboard as the first summary of HR activity, employee totals, attendance status, leave activity, and other operational snapshots.',
                    'steps' => [
                        'Open Dashboard from the sidebar after login.',
                        'Review each chart and summary card to understand current HR conditions.',
                        'Use the latest activity sections to jump into records that need follow up.',
                    ],
                ],
                [
                    'title' => 'Employee Management',
                    'description' => 'Manage employee master records and the supporting employee detail tabs.',
                    'steps' => [
                        'Open Employees to search and filter by name, NIP, email, status, company, department, division, section, or work location.',
                        'Create or edit employee profiles with organization, job, personal, facility, and blood type information.',
                        'Open an employee detail page to manage Information, Contracts, Family, Education, Work History, Attendances, and Leave Balance.',
                        'Use Documents and Emergency Contacts in the employee detail area to complete employee records.',
                    ],
                ],
                [
                    'title' => 'Attendance',
                    'description' => 'Review attendance records and handle CSV-based attendance export or import.',
                    'steps' => [
                        'Open Attendances to search employee attendance and filter by status, date, company, department, division, section, or work location.',
                        'Use Export to download attendance data matching the current filters.',
                        'Use Download Template before import so the CSV columns match the required format.',
                        'Import the filled template and review row-level import errors when shown.',
                    ],
                ],
                [
                    'title' => 'Leave Management',
                    'description' => 'Manage leave requests, leave types, leave settings, approvals, holidays, and approval workflows.',
                    'steps' => [
                        'Open Leave Requests to create, update, delete, search, and filter leave records.',
                        'Use Bulk Create Leave Request when an admin needs to create the same leave request for multiple employees.',
                        'Manage Leave Types and max days from the Leave Management menu.',
                        'Manage Leave Settings for advance leave and rollover rules per company.',
                        'Manage Holidays so approved leave request inclusive days are recalculated when holiday dates are created, updated, or deleted.',
                        'Manage Approval Workflows to define approval rules by organization scope and workflow type.',
                    ],
                ],
                [
                    'title' => 'Organization Master Data',
                    'description' => 'Maintain the company structure used by employee filters, approvals, leave, attendance, payroll, and reporting.',
                    'steps' => [
                        'Manage Companies, Work Locations, Departments, Divisions, and Sections from Master Data.',
                        'Use Work Location latitude, longitude, and radius fields for location-aware attendance rules.',
                        'Keep names consistent because they are reused in filters and employee forms.',
                    ],
                ],
                [
                    'title' => 'Reference Master Data',
                    'description' => 'Maintain reusable lookup data used across HR forms.',
                    'steps' => [
                        'Manage Levels, Religions, Job Positions, Contract Types, Education Types, Family Types, Relationships, Document Types, Shifts, Roles, and Modules.',
                        'Use Roles to set RBAC permissions for module access.',
                        'Keep Modules aligned with permissions and activity log grouping.',
                    ],
                ],
                [
                    'title' => 'Administration',
                    'description' => 'Manage users, roles, permissions, audit records, login attempts, and system settings.',
                    'steps' => [
                        'Open User Roles to assign or update a user role.',
                        'Open Roles from Master Data or Settings to configure RBAC permissions.',
                        'Open Activity Logs to review who created, updated, or deleted records and the before or after data.',
                        'Open Login Attempts to review login success records.',
                    ],
                ],
                [
                    'title' => 'Facilities',
                    'description' => 'Manage facility criteria and facilities that can be connected to employees.',
                    'steps' => [
                        'Create Facility Criteria first when facilities need grouping.',
                        'Create Facilities and select one or more criteria.',
                        'Assign facilities from the employee create or edit form.',
                    ],
                ],
                [
                    'title' => 'Payroll',
                    'description' => 'Manage payroll periods and employee payroll calculation records.',
                    'steps' => [
                        'Create Payroll Periods by company, month, year, start date, end date, and status.',
                        'Create Payroll records for employees with salary, allowance, deduction, BPJS, tax, take home pay, and payment status.',
                        'Use the list search to find payroll records by available text fields.',
                    ],
                ],
                [
                    'title' => 'Performance Management',
                    'description' => 'Manage employee KPI targets and 360 feedback records from the Performance sidebar group.',
                    'steps' => [
                        'Open KPI to create employee performance targets with period, target, actual result, weight, score, status, and notes.',
                        'Open 360 Feedback to record reviewer feedback by manager, peer, subordinate, self, or external reviewer.',
                        'Use score fields and comments to summarize strengths, improvements, and overall performance by period.',
                    ],
                ],
                [
                    'title' => 'Transfers',
                    'description' => 'Record employee transfers between companies, departments, divisions, sections, work locations, or other transfer scopes.',
                    'steps' => [
                        'Create Transfer Types to describe the available transfer categories.',
                        'Create Transfers by selecting employee, type, source ID, destination ID, reason, and status.',
                        'Update the transfer status as the transfer process moves forward.',
                    ],
                ],
                [
                    'title' => 'Notifications',
                    'description' => 'Create notifications for organization scopes or personal users.',
                    'steps' => [
                        'Create notifications with title, message, optional file, audience scope, and status.',
                        'Use organization fields for company, department, division, section, or work location broadcasts.',
                        'Use personal users for user-specific notifications.',
                    ],
                ],
                [
                    'title' => 'Chat',
                    'description' => 'Use chat for quick communication between users.',
                    'steps' => [
                        'Use the floating chat button at the bottom right from authenticated pages.',
                        'Click New, select a user, and create or open the chat room without leaving the page.',
                        'Send messages from the active room; the logged-in user is always the sender.',
                    ],
                ],
                [
                    'title' => 'Profile and Settings',
                    'description' => 'Manage personal profile data and system support pages.',
                    'steps' => [
                        'Open Profile from the user menu to update your own account data.',
                        'Open Settings to reach RBAC guidance and application support pages.',
                        'Open Documentation from Settings when users need a single guide for module workflows.',
                    ],
                ],
            ],
        ]);
    }
}
