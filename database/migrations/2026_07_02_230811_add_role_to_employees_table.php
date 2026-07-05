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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('role')->default('usuario')->after('company_id');
        });

        // Initialize role column from users table for existing records
        DB::statement('
            UPDATE employees e
            INNER JOIN users u ON e.user_id = u.id
            SET e.role = u.role
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
