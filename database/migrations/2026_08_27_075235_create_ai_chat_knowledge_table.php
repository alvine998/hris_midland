<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->string('title', 255);
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('ai_chat_knowledge', function (Blueprint $table) {
                $table->fullText(['title', 'content']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_knowledge');
    }
};
