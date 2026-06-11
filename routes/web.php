<?php

use App\Http\Controllers\AdminCrudController;
use App\Http\Controllers\ApprovalWorkflowController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EducationHistoryController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\Feedback360Controller;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PerformanceReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\WorkHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');

    Route::get('/verify-otp', [OtpVerificationController::class, 'showVerifyForm'])->name('verify-otp');
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->middleware('throttle:otp')->name('otp.verify');
    Route::post('/verify-otp/resend', [OtpVerificationController::class, 'resend'])->middleware('throttle:otp-resend')->name('otp.resend');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->middleware('throttle:password-reset')->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('throttle:password-reset')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/documentation', [SettingsController::class, 'documentation'])->name('settings.documentation');

    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

    Route::post('/families', [FamilyController::class, 'store'])->name('families.store');
    Route::put('/families/{family}', [FamilyController::class, 'update'])->name('families.update');
    Route::delete('/families/{family}', [FamilyController::class, 'destroy'])->name('families.destroy');

    Route::post('/education-histories', [EducationHistoryController::class, 'store'])->name('education-histories.store');
    Route::put('/education-histories/{educationHistory}', [EducationHistoryController::class, 'update'])->name('education-histories.update');
    Route::delete('/education-histories/{educationHistory}', [EducationHistoryController::class, 'destroy'])->name('education-histories.destroy');

    Route::post('/work-histories', [WorkHistoryController::class, 'store'])->name('work-histories.store');
    Route::put('/work-histories/{workHistory}', [WorkHistoryController::class, 'update'])->name('work-histories.update');
    Route::delete('/work-histories/{workHistory}', [WorkHistoryController::class, 'destroy'])->name('work-histories.destroy');

    Route::post('/emergency-contacts', [EmergencyContactController::class, 'store'])->name('emergency-contacts.store');
    Route::put('/emergency-contacts/{emergencyContact}', [EmergencyContactController::class, 'update'])->name('emergency-contacts.update');
    Route::delete('/emergency-contacts/{emergencyContact}', [EmergencyContactController::class, 'destroy'])->name('emergency-contacts.destroy');

    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::post('/leave-requests/bulk', [LeaveRequestController::class, 'bulkStore'])->name('leave-requests.bulk-store');
    Route::put('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update'])->name('leave-requests.update');
    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');

    Route::post('/leave-approvals', [LeaveApprovalController::class, 'store'])->name('leave-approvals.store');
    Route::put('/leave-approvals/{leaveApproval}', [LeaveApprovalController::class, 'update'])->name('leave-approvals.update');
    Route::delete('/leave-approvals/{leaveApproval}', [LeaveApprovalController::class, 'destroy'])->name('leave-approvals.destroy');

    Route::post('/employee-shifts', [EmployeeShiftController::class, 'store'])->name('employee-shifts.store');
    Route::put('/employee-shifts/{employeeShift}', [EmployeeShiftController::class, 'update'])->name('employee-shifts.update');
    Route::delete('/employee-shifts/{employeeShift}', [EmployeeShiftController::class, 'destroy'])->name('employee-shifts.destroy');

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');
    Route::get('/attendances/template', [AttendanceController::class, 'template'])->name('attendances.template');
    Route::post('/attendances/import', [AttendanceController::class, 'import'])->name('attendances.import');

    Route::get('/employee-options', [AdminCrudController::class, 'employeeOptions'])->name('employee-options.index');
    Route::get('/admin-crud/{resource}', [AdminCrudController::class, 'index'])->name('admin-crud.index');
    Route::post('/admin-crud/{resource}', [AdminCrudController::class, 'store'])->name('admin-crud.store');
    Route::put('/admin-crud/{resource}/{id}', [AdminCrudController::class, 'update'])->name('admin-crud.update');
    Route::delete('/admin-crud/{resource}/{id}', [AdminCrudController::class, 'destroy'])->name('admin-crud.destroy');

    Route::get('/leave-management/holidays', [HolidayController::class, 'index'])->name('leave-management.holidays.index');
    Route::post('/leave-management/holidays', [HolidayController::class, 'store'])->name('leave-management.holidays.store');
    Route::put('/leave-management/holidays/{holiday}', [HolidayController::class, 'update'])->name('leave-management.holidays.update');
    Route::delete('/leave-management/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('leave-management.holidays.destroy');

    Route::get('/communication/chats', [ChatController::class, 'index'])->name('communication.chats.index');
    Route::post('/communication/chats', [ChatController::class, 'store'])->name('communication.chats.store');
    Route::get('/communication/chats/{chat}/messages', [ChatController::class, 'messages'])->name('communication.chats.messages');
    Route::post('/communication/chats/{chat}/messages', [ChatController::class, 'send'])->name('communication.chats.send');

    Route::middleware('permission:*')->group(function () {
        Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
        Route::post('/user-roles', [UserRoleController::class, 'store'])->name('user-roles.store');
        Route::put('/user-roles/{userRole}', [UserRoleController::class, 'update'])->name('user-roles.update');
        Route::delete('/user-roles/{userRole}', [UserRoleController::class, 'destroy'])->name('user-roles.destroy');
    });

    Route::get('/performance/feedback360', [Feedback360Controller::class, 'index'])->name('performance.feedback360.index');
    Route::get('/performance/feedback360/create', [Feedback360Controller::class, 'create'])->name('performance.feedback360.create');
    Route::post('/performance/feedback360', [Feedback360Controller::class, 'store'])->name('performance.feedback360.store');
    Route::get('/performance/feedback360/{feedback}', [Feedback360Controller::class, 'show'])->name('performance.feedback360.show');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/master-data/companies', [OrganizationController::class, 'companies'])->name('master-data.companies');
    Route::get('/master-data/work-locations', [OrganizationController::class, 'workLocations'])->name('master-data.work-locations');
    Route::get('/master-data/departments', [OrganizationController::class, 'departments'])->name('master-data.departments');
    Route::get('/master-data/divisions', [OrganizationController::class, 'divisions'])->name('master-data.divisions');
    Route::get('/master-data/sections', [OrganizationController::class, 'sections'])->name('master-data.sections');

    Route::post('/master-data/companies', [OrganizationController::class, 'storeCompany'])->name('master-data.companies.store');
    Route::put('/master-data/companies/{company}', [OrganizationController::class, 'updateCompany'])->name('master-data.companies.update');
    Route::delete('/master-data/companies/{company}', [OrganizationController::class, 'destroyCompany'])->name('master-data.companies.destroy');

    Route::post('/master-data/work-locations', [OrganizationController::class, 'storeWorkLocation'])->name('master-data.work-locations.store');
    Route::put('/master-data/work-locations/{workLocation}', [OrganizationController::class, 'updateWorkLocation'])->name('master-data.work-locations.update');
    Route::delete('/master-data/work-locations/{workLocation}', [OrganizationController::class, 'destroyWorkLocation'])->name('master-data.work-locations.destroy');

    Route::post('/master-data/departments', [OrganizationController::class, 'storeDepartment'])->name('master-data.departments.store');
    Route::put('/master-data/departments/{department}', [OrganizationController::class, 'updateDepartment'])->name('master-data.departments.update');
    Route::delete('/master-data/departments/{department}', [OrganizationController::class, 'destroyDepartment'])->name('master-data.departments.destroy');

    Route::post('/master-data/divisions', [OrganizationController::class, 'storeDivision'])->name('master-data.divisions.store');
    Route::put('/master-data/divisions/{division}', [OrganizationController::class, 'updateDivision'])->name('master-data.divisions.update');
    Route::delete('/master-data/divisions/{division}', [OrganizationController::class, 'destroyDivision'])->name('master-data.divisions.destroy');

    Route::post('/master-data/sections', [OrganizationController::class, 'storeSection'])->name('master-data.sections.store');
    Route::put('/master-data/sections/{section}', [OrganizationController::class, 'updateSection'])->name('master-data.sections.update');
    Route::delete('/master-data/sections/{section}', [OrganizationController::class, 'destroySection'])->name('master-data.sections.destroy');

    Route::get('/master-data/levels', [ReferenceController::class, 'levels'])->name('master-data.levels');
    Route::get('/master-data/religions', [ReferenceController::class, 'religions'])->name('master-data.religions');
    Route::get('/master-data/job-positions', [ReferenceController::class, 'jobPositions'])->name('master-data.job-positions');
    Route::get('/master-data/modules', [ReferenceController::class, 'modules'])->name('master-data.modules');
    Route::get('/master-data/roles', [ReferenceController::class, 'roles'])->name('master-data.roles');
    Route::get('/master-data/contract-types', [ReferenceController::class, 'contractTypes'])->name('master-data.contract-types');
    Route::get('/master-data/education-types', [ReferenceController::class, 'educationTypes'])->name('master-data.education-types');
    Route::get('/master-data/family-types', [ReferenceController::class, 'familyTypes'])->name('master-data.family-types');
    Route::get('/master-data/relationships', [RelationshipController::class, 'index'])->name('master-data.relationships');
    Route::get('/master-data/document-types', [DocumentTypeController::class, 'index'])->name('master-data.document-types');
    Route::get('/master-data/leave-types', [LeaveTypeController::class, 'index'])->name('master-data.leave-types');
    Route::get('/master-data/shifts', [ShiftController::class, 'index'])->name('master-data.shifts');
    Route::get('/master-data/approval-workflows', [ApprovalWorkflowController::class, 'index'])->name('master-data.approval-workflows');

    Route::post('/master-data/levels', [ReferenceController::class, 'storeLevel'])->name('master-data.levels.store');
    Route::put('/master-data/levels/{level}', [ReferenceController::class, 'updateLevel'])->name('master-data.levels.update');
    Route::delete('/master-data/levels/{level}', [ReferenceController::class, 'destroyLevel'])->name('master-data.levels.destroy');

    Route::post('/master-data/religions', [ReferenceController::class, 'storeReligion'])->name('master-data.religions.store');
    Route::put('/master-data/religions/{religion}', [ReferenceController::class, 'updateReligion'])->name('master-data.religions.update');
    Route::delete('/master-data/religions/{religion}', [ReferenceController::class, 'destroyReligion'])->name('master-data.religions.destroy');

    Route::post('/master-data/job-positions', [ReferenceController::class, 'storeJobPosition'])->name('master-data.job-positions.store');
    Route::put('/master-data/job-positions/{jobPosition}', [ReferenceController::class, 'updateJobPosition'])->name('master-data.job-positions.update');
    Route::delete('/master-data/job-positions/{jobPosition}', [ReferenceController::class, 'destroyJobPosition'])->name('master-data.job-positions.destroy');

    Route::post('/master-data/modules', [ReferenceController::class, 'storeModule'])->name('master-data.modules.store');
    Route::put('/master-data/modules/{module}', [ReferenceController::class, 'updateModule'])->name('master-data.modules.update');
    Route::delete('/master-data/modules/{module}', [ReferenceController::class, 'destroyModule'])->name('master-data.modules.destroy');

    Route::post('/master-data/roles', [ReferenceController::class, 'storeRole'])->name('master-data.roles.store');
    Route::put('/master-data/roles/{role}', [ReferenceController::class, 'updateRole'])->name('master-data.roles.update');
    Route::delete('/master-data/roles/{role}', [ReferenceController::class, 'destroyRole'])->name('master-data.roles.destroy');

    Route::post('/master-data/contract-types', [ReferenceController::class, 'storeContractType'])->name('master-data.contract-types.store');
    Route::put('/master-data/contract-types/{contractType}', [ReferenceController::class, 'updateContractType'])->name('master-data.contract-types.update');
    Route::delete('/master-data/contract-types/{contractType}', [ReferenceController::class, 'destroyContractType'])->name('master-data.contract-types.destroy');

    Route::post('/master-data/education-types', [ReferenceController::class, 'storeEducationType'])->name('master-data.education-types.store');
    Route::put('/master-data/education-types/{educationType}', [ReferenceController::class, 'updateEducationType'])->name('master-data.education-types.update');
    Route::delete('/master-data/education-types/{educationType}', [ReferenceController::class, 'destroyEducationType'])->name('master-data.education-types.destroy');

    Route::post('/master-data/family-types', [ReferenceController::class, 'storeFamilyType'])->name('master-data.family-types.store');
    Route::put('/master-data/family-types/{familyType}', [ReferenceController::class, 'updateFamilyType'])->name('master-data.family-types.update');
    Route::delete('/master-data/family-types/{familyType}', [ReferenceController::class, 'destroyFamilyType'])->name('master-data.family-types.destroy');

    Route::post('/master-data/relationships', [RelationshipController::class, 'store'])->name('master-data.relationships.store');
    Route::put('/master-data/relationships/{relationship}', [RelationshipController::class, 'update'])->name('master-data.relationships.update');
    Route::delete('/master-data/relationships/{relationship}', [RelationshipController::class, 'destroy'])->name('master-data.relationships.destroy');

    Route::post('/master-data/document-types', [DocumentTypeController::class, 'store'])->name('master-data.document-types.store');
    Route::put('/master-data/document-types/{documentType}', [DocumentTypeController::class, 'update'])->name('master-data.document-types.update');
    Route::delete('/master-data/document-types/{documentType}', [DocumentTypeController::class, 'destroy'])->name('master-data.document-types.destroy');

    Route::post('/master-data/leave-types', [LeaveTypeController::class, 'store'])->name('master-data.leave-types.store');
    Route::put('/master-data/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('master-data.leave-types.update');
    Route::delete('/master-data/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('master-data.leave-types.destroy');

    Route::post('/master-data/shifts', [ShiftController::class, 'store'])->name('master-data.shifts.store');
    Route::put('/master-data/shifts/{shift}', [ShiftController::class, 'update'])->name('master-data.shifts.update');
    Route::delete('/master-data/shifts/{shift}', [ShiftController::class, 'destroy'])->name('master-data.shifts.destroy');

    Route::post('/master-data/approval-workflows', [ApprovalWorkflowController::class, 'store'])->name('master-data.approval-workflows.store');
    Route::put('/master-data/approval-workflows/{approvalWorkflow}', [ApprovalWorkflowController::class, 'update'])->name('master-data.approval-workflows.update');
    Route::delete('/master-data/approval-workflows/{approvalWorkflow}', [ApprovalWorkflowController::class, 'destroy'])->name('master-data.approval-workflows.destroy');

    Route::get('/performance/report', [PerformanceReportController::class, 'index'])->name('performance.report');
});
