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
        Schema::table('exams', function (Blueprint $table) {
            $table->enum('type', ['scheduled', 'mock'])->default('scheduled');
            $table->dateTime('exam_end')->nullable()->change();
            $table->dateTime('application_start')->nullable()->change();
            $table->dateTime('application_end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop the 'type' column
            $table->dropColumn('type');

            // Revert the nullable changes (assumes originally they were NOT nullable)
            $table->dateTime('exam_end')->nullable(false)->change();
            $table->dateTime('application_start')->nullable(false)->change();
            $table->dateTime('application_end')->nullable(false)->change();
        });
    }
};
