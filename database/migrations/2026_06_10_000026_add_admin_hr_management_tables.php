<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_workflows', function (Blueprint $table) {
            $table->string('type')->nullable()->after('description');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->json('facility_ids')->nullable()->after('work_location_id');
            $table->string('blood_type')->nullable()->after('facility_ids');
        });

        Schema::table('work_locations', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('type');
            $table->string('longitude')->nullable()->after('latitude');
            $table->integer('radius')->nullable()->after('longitude');
        });

        Schema::table('leave_types', function (Blueprint $table) {
            $table->integer('max_days')->nullable()->after('name');
        });

        Schema::create('leave_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_advance_leave')->default(false);
            $table->integer('max_advance_leave')->nullable();
            $table->boolean('is_rollover')->default(false);
            $table->integer('max_rollover')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->text('action');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('success')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('facility_criterias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->json('facility_criteria_ids')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('month');
            $table->year('year');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->bigInteger('basic_salary')->default(0);
            $table->bigInteger('allowance_total')->default(0);
            $table->bigInteger('deduction_total')->default(0);
            $table->bigInteger('bpjs_total')->default(0);
            $table->bigInteger('tax_pph21')->default(0);
            $table->bigInteger('take_home_pay')->default(0);
            $table->string('status')->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transfer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transfer_type_id')->nullable()->constrained('transfer_types')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->integer('transfer_from')->nullable();
            $table->integer('transfer_to')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_location_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_ids')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->text('file')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->json('messages')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->nullable();
            $table->foreignId('user_one_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_two_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('type')->default('national');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chat_rooms');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('transfer_types');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('facility_criterias');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('leave_settings');

        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('max_days');
        });

        Schema::table('work_locations', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['facility_ids', 'blood_type']);
        });

        Schema::table('approval_workflows', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
