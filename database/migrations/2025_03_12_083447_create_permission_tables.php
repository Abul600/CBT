<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        // ✅ Only create 'roles' table if it doesn't exist
        if (!Schema::hasTable($tableNames['roles'])) {
            Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
                $table->id(); 
                if ($teams || config('permission.testing')) { 
                    $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                    $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
                }
                $table->string('name')->unique();
                $table->string('guard_name')->default('web');
                $table->timestamps();
            });
        }

        // ✅ Create 'permissions' table
        if (!Schema::hasTable($tableNames['permissions'])) {
            Schema::create($tableNames['permissions'], static function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('guard_name')->default('web');
                $table->timestamps();
            });
        }

        // ✅ Create pivot tables for role-permission assignments
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($pivotRole, $columnNames) {
            $table->unsignedBigInteger($pivotRole);
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->string('model_type');
            $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type']);
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($pivotPermission, $columnNames) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->string('model_type');
            $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type']);
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);
            $table->primary([$pivotPermission, $pivotRole]);
        });

        app('cache')->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['permissions']);
        
        // ❌ DO NOT drop 'roles' if it's managed elsewhere
        // Schema::dropIfExists($tableNames['roles']); 
    }
};
