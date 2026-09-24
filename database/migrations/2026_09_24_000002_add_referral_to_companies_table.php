<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->after('subscription_selected_at');
            $table->unsignedBigInteger('referente_id')->nullable()->after('referral_code');
            $table->foreign('referente_id')->references('id')->on('referentes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['referente_id']);
            $table->dropColumn(['referral_code', 'referente_id']);
        });
    }
};
