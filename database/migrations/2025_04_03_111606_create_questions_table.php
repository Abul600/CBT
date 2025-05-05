<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // Relationships
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

            // Content
            $table->text('question_text');
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();

            // Metadata
            $table->enum('correct_option', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('type', ['mcq1', 'mcq2', 'descriptive']);
            $table->integer('marks')->default(1);

            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                  ->default('draft');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
