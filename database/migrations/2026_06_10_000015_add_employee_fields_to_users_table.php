<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->after('id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('status')
                ->after('password')
                ->default('active');
            $table->text('fcm_token')
                ->after('status')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['employee_id', 'status', 'fcm_token']);
        });
    }
};
