<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('content_type', ['video', 'audio', 'pdf', 'html', 'text'])->default('text');
            $table->longText('content')->nullable();
            $table->string('media_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
            $table->unique(['course_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
