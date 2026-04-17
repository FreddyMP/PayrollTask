<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->date('overtime_date')->nullable()->after('end_date');
            $table->time('overtime_start')->nullable()->after('overtime_date');
            $table->time('overtime_end')->nullable()->after('overtime_start');
            $table->decimal('overtime_hours', 5, 2)->nullable()->after('overtime_end');
            $table->unsignedBigInteger('approved_by_user_id')->nullable()->after('overtime_hours');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['overtime_date', 'overtime_start', 'overtime_end', 'overtime_hours', 'approved_by_user_id']);
        });
    }
};
