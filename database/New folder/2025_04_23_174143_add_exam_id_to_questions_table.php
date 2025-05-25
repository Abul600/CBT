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
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'exam_id')) {
                $table->unsignedBigInteger('exam_id')->after('paper_setter_id');
                $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->dropForeign(['exam_id']);
                $table->dropColumn('exam_id');
            }
        });
    }
};
