<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('title');
            $table->text('description');
            $table->string('file_path'); // Path to uploaded files

            // Foreign keys
            $table->foreignId('district_id')->constrained()->onDelete('cascade'); // Optional: district-specific
            $table->foreignId('original_exam_id')->constrained('exams'); // Links to converted exam

            // Optional answer key or explanations for descriptive questions
            $table->text('descriptive_answers')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};
