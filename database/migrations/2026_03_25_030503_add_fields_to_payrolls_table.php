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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('extras', 15, 2)->default(0)->after('gross_salary');
            $table->decimal('descuentos', 15, 2)->default(0)->after('extras');
            $table->decimal('ars', 15, 2)->default(0)->after('descuentos');
            $table->decimal('afp', 15, 2)->default(0)->after('ars');
            $table->decimal('isr', 15, 2)->default(0)->after('afp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['extras', 'descuentos', 'ars', 'afp', 'isr']);
        });
    }
};
