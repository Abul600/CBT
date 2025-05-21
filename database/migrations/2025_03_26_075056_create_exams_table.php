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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration'); // Duration in minutes

            // Application and exam scheduling fields (nullable for mock exams)
            $table->dateTime('application_start')->nullable();
            $table->dateTime('application_end')->nullable();
            $table->dateTime('exam_start')->nullable();
            $table->dateTime('exam_end')->nullable();

            // Foreign keys
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('district_id')->nullable()->constrained();

            // Additional status fields
            $table->string('status')->default('draft');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
