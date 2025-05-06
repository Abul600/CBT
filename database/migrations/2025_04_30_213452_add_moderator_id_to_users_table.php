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
        Schema::table('users', function (Blueprint $table) {
            // Add column only if it doesn't exist
            if (!Schema::hasColumn('users', 'moderator_id')) {
                $table->unsignedBigInteger('moderator_id')->nullable()->after('role');
                $table->foreign('moderator_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
                $table->index('moderator_id'); // Optional but improves performance
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'moderator_id')) {
                $table->dropForeign(['moderator_id']);
                $table->dropIndex(['moderator_id']);
                $table->dropColumn('moderator_id');
            }
        });
    }
};
