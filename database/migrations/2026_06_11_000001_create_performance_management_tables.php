<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('period')->nullable();
            $table->decimal('target', 8, 2)->nullable();
            $table->decimal('actual', 8, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('feedback360s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reviewer_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_type')->default('peer');
            $table->string('period')->nullable();
            $table->unsignedTinyInteger('communication_score')->nullable();
            $table->unsignedTinyInteger('teamwork_score')->nullable();
            $table->unsignedTinyInteger('leadership_score')->nullable();
            $table->unsignedTinyInteger('technical_score')->nullable();
            $table->unsignedTinyInteger('overall_score')->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->default('draft');
            $table->date('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback360s');
        Schema::dropIfExists('kpis');
    }
};
