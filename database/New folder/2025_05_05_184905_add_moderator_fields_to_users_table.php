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
        Schema::table('users', function (Blueprint $table) {
            // Add district_id and is_moderator if not already present
            $table->foreignId('district_id')->nullable()->constrained();
            $table->boolean('is_moderator')->default(false);
        });

        // Partial unique index for PostgreSQL
        DB::statement('
            CREATE UNIQUE INDEX users_active_moderator_district_unique 
            ON users (district_id) 
            WHERE (role = \'moderator\' AND is_moderator = TRUE)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial index
        DB::statement('DROP INDEX IF EXISTS users_active_moderator_district_unique');

        Schema::table('users', function (Blueprint $table) {
            // Drop added columns
            $table->dropForeign(['district_id']);
            $table->dropColumn(['district_id', 'is_moderator']);
        });
    }
};
