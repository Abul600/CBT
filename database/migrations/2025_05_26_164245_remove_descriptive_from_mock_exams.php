<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Question; // Ensure Question model is available
use App\Models\Exam;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all descriptive questions that belong to mock exams
        $invalidQuestions = Question::whereHas('exam', function($query) {
            $query->where('type', 'mock');
        })->where('type', 'descriptive')->get();

        // Permanently delete these invalid questions
        Question::whereIn('id', $invalidQuestions->pluck('id'))->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This destructive action cannot be reversed.
      
    }
};
