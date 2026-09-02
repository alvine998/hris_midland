<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_stock_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->date('survey_date')->nullable();
            $table->foreignId('surveyor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('interest_level')->nullable();
            $table->text('feedback')->nullable();
            $table->string('next_action')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'interest_level']);
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_customers');
    }
};
