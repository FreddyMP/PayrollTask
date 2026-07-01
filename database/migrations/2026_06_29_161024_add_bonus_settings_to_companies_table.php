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
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('bonus_payment_method', ['payroll', 'separate'])->nullable()->after('payroll_frequency');
            $table->enum('bonus_biweekly_split', ['both', 'q1', 'q2'])->nullable()->after('bonus_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['bonus_payment_method', 'bonus_biweekly_split']);
        });
    }
};
