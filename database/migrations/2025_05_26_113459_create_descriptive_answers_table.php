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
        Schema::create('descriptive_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_id')->constrained()->onDelete('cascade');

            // Nullable user_id (student who answered)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            $table->foreignId('exam_id')->constrained()->onDelete('cascade');

            // ✅ New: submission reference
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');

            $table->text('answer');
            $table->integer('marks')->nullable();

            $table->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('graded_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('descriptive_answers');
    }
};
