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
        Schema::table('devices', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('name');
            $table->enum('type', ['laptop', 'desktop', 'tablet', 'phone', 'otros'])->default('otros')->after('brand');
            $table->enum('status', ['activo', 'inactivo', 'mantenimiento'])->default('activo')->after('type');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['brand', 'type', 'status', 'employee_id']);
        });
    }
};
