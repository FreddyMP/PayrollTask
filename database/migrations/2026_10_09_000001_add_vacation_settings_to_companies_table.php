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
            $table->boolean('vacation_payment_enabled')->default(true)->after('bonus_biweekly_split');
            $table->enum('vacation_payment_method', ['payroll', 'separate'])->default('payroll')->after('vacation_payment_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['vacation_payment_enabled', 'vacation_payment_method']);
        });
    }
};
