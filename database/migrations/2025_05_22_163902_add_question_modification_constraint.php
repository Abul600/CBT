<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // WARNING: This will fail on MySQL due to unsupported subquery in CHECK
        DB::statement('
            ALTER TABLE exam_question
            ADD CONSTRAINT check_exam_released
            CHECK (
                NOT EXISTS (
                    SELECT 1 FROM exams 
                    WHERE exams.id = exam_question.exam_id 
                    AND exams.is_released = true
                )
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE exam_question DROP CONSTRAINT check_exam_released');
    }
};
