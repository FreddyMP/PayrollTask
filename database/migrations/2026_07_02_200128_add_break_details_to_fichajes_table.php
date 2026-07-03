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
        Schema::table('fichajes', function (Blueprint $table) {
            $table->time('break_start')->nullable()->after('clock_out');
            $table->time('break_end')->nullable()->after('break_start');
            $table->decimal('total_hours', 8, 2)->nullable()->after('break_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichajes', function (Blueprint $table) {
            $table->dropColumn(['break_start', 'break_end', 'total_hours']);
        });
    }
};
