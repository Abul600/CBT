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

            // Basic exam info
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration'); // In minutes

            // Application & exam scheduling
            $table->dateTime('application_start')->nullable();
            $table->dateTime('application_end')->nullable();
            $table->dateTime('exam_start')->nullable();
            $table->dateTime('exam_end')->nullable();

            // Status and control flags
            $table->enum('type', ['mock', 'scheduled'])->default('scheduled');
            $table->string('status')->default('draft');
            $table->boolean('is_active')->default(true);
            $table->boolean('converted_to_mock')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->boolean('is_released')->default(false);
            $table->timestamp('released_at')->nullable();
            $table->boolean('auto_gradable')->default(true);

            // Foreign keys
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('district_id')->nullable()->constrained();

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
