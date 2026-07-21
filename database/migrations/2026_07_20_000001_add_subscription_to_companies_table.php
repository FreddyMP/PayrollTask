<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subscription_plan')->nullable()->after('status'); // starter, growth, business, enterprise
            $table->timestamp('subscription_selected_at')->nullable()->after('subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_selected_at']);
        });
    }
};
