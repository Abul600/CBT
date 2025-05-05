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
            // Add 'moderator_id' column after 'role' (adjust placement if needed)
            $table->unsignedBigInteger('moderator_id')->nullable()->after('role');

            // Add foreign key referencing 'users.id'
            $table->foreign('moderator_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // If moderator is deleted, set field to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['moderator_id']);
            $table->dropColumn('moderator_id');
        });
    }
};
