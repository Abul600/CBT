<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->unsignedBigInteger('exam_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Replace with an existing exam_id from your DB, for example: 1
        $fallbackExamId = 1;

        // Check if the fallback ID exists
        if (DB::table('exams')->where('id', $fallbackExamId)->exists()) {
            DB::table('questions')
                ->whereNull('exam_id')
                ->update(['exam_id' => $fallbackExamId]);
        } else {
            throw new \Exception("Fallback exam_id ($fallbackExamId) does not exist.");
        }

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->unsignedBigInteger('exam_id')->nullable(false)->change();
            }
        });
    }
};
