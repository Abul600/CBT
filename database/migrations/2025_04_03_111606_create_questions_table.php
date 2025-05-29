<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('paper_setter_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('exam_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('moderator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('sent_to_moderator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('district_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('study_material_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Question content
            $table->text('question_text');
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();

            // Use lowercase only for correct_option
            $table->string('correct_option')->nullable()->checkIn(['a', 'b', 'c', 'd']);

            // Type and scoring
            $table->enum('type', ['mcq1', 'mcq2', 'descriptive']);
            $table->integer('marks')->default(1);

            // Status and tracking
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
