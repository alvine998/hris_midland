<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('unit_code');
            $table->string('type')->nullable();
            $table->string('block')->nullable();
            $table->decimal('land_size', 10, 2)->nullable();
            $table->decimal('building_size', 10, 2)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('status')->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'unit_code']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_stocks');
    }
};
