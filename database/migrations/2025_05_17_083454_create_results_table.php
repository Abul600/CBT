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
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete(); // optional manual grader

            // Scores
            $table->integer('score'); // total score (mcq + descriptive)
            $table->integer('mcq_score')->default(0); // score from mcq
            $table->integer('descriptive_score')->nullable(); // descriptive part
            $table->integer('total')->nullable(); // total marks
            $table->float('percentage')->nullable(); // score / total * 100

            // Meta info
            $table->integer('time_taken')->nullable(); // in seconds or minutes
            $table->integer('attempt_number')->default(1); // for multiple attempts
            $table->timestamp('completed_at')->nullable(); // actual completion time

            // Status
            $table->string('status')->default('completed'); // e.g. completed, failed, passed
            $table->boolean('passed')->default(false); // pass/fail flag
            $table->boolean('reviewed')->default(false); // for manual review tracking
            $table->boolean('auto_graded')->default(false); // whether graded by system

            // Feedback
            $table->text('remarks')->nullable(); // manual review feedback

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
