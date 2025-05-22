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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->integer('total')->nullable(); // total marks
            $table->float('percentage')->nullable(); // score / total * 100
            $table->integer('time_taken')->nullable(); // in seconds or minutes
            $table->integer('attempt_number')->default(1); // if multiple attempts allowed
            $table->string('status')->default('completed'); // e.g., completed, failed, passed, pending
            $table->boolean('passed')->default(false); // simple boolean for pass/fail
            $table->text('remarks')->nullable(); // manual review feedback
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete(); // optional manual grader
            $table->timestamp('completed_at')->nullable(); // actual exam finish time
            $table->boolean('reviewed')->default(false); // for manual review tracking
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
