<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('status')->default('planning');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'progress']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
