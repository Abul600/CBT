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
            $table->unsignedBigInteger('moderator_id')->nullable()->after('role');

            $table->foreign('moderator_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->index('moderator_id'); // Optional but good for performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['moderator_id']);
            $table->dropIndex(['moderator_id']); // Drop index before column
            $table->dropColumn('moderator_id');
        });
    }
};
