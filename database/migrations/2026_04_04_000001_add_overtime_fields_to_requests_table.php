<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['overtime_date', 'overtime_start', 'overtime_end', 'overtime_hours', 'approved_by_user_id']);
        });
    }
};
