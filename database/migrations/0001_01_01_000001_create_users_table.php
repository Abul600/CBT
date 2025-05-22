<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Create the 'users' table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('district')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            $table->boolean('is_active')->default(true);

            // Role columns
            $table->string('role')->default('student');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');

            // Moderator-related columns
            $table->unsignedBigInteger('moderator_id')->nullable();
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');
            $table->index('moderator_id');

            $table->foreignId('district_id')->nullable()->constrained();
            $table->boolean('is_moderator')->default(false);

            // Two-factor auth fields
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        // Create partial unique index for PostgreSQL
        DB::statement("
            CREATE UNIQUE INDEX users_active_moderator_district_unique 
            ON users (district_id) 
            WHERE (role = 'moderator' AND is_moderator = TRUE)
        ");

        // Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Drop index
        DB::statement('DROP INDEX IF EXISTS users_active_moderator_district_unique');

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
