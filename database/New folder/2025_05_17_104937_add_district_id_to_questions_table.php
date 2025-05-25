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
            // 1. Add as nullable
            $table->unsignedBigInteger('district_id')->nullable()->after('id');
        });

        // 2. Set default value for existing records (replace 2 with your default district ID if different)
        DB::statement("UPDATE questions SET district_id = 2");

        // 3. Change to not nullable
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
        });

        // 4. Add foreign key constraint
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('district_id')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
        });
    }
};
