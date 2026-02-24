<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('version')->default(1);
            $table->foreignId('parent_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->json('settings')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('instructor_id');
            $table->index('tenant_id');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
