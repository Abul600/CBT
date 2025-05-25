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
            if (!Schema::hasColumn('exams', 'converted_to_mock')) {
                $table->boolean('converted_to_mock')->default(false);
            }

            if (!Schema::hasColumn('exams', 'converted_at')) {
                $table->timestamp('converted_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'converted_to_mock')) {
                $table->dropColumn('converted_to_mock');
            }

            if (Schema::hasColumn('exams', 'converted_at')) {
                $table->dropColumn('converted_at');
            }
        });
    }
};
