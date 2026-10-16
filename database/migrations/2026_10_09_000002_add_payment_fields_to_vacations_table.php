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
        Schema::table('vacations', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('status');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
            $table->foreignId('payment_payroll_id')->nullable()->constrained('payrolls')->nullOnDelete()->after('paid_at');
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_payroll_id');
            $table->decimal('payment_ars', 10, 2)->nullable()->after('payment_amount');
            $table->decimal('payment_afp', 10, 2)->nullable()->after('payment_ars');
            $table->decimal('payment_isr', 10, 2)->nullable()->after('payment_afp');
            $table->decimal('payment_net', 10, 2)->nullable()->after('payment_isr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropForeign(['payment_payroll_id']);
            $table->dropColumn([
                'is_paid',
                'paid_at',
                'payment_payroll_id',
                'payment_amount',
                'payment_ars',
                'payment_afp',
                'payment_isr',
                'payment_net',
            ]);
        });
    }
};
