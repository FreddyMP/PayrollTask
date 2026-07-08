<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fichajes', function (Blueprint $table) {
            $table->enum('work_mode', ['presencial', 'remoto'])
                ->default('presencial')
                ->after('clock_in');
        });
    }

    public function down(): void
    {
        Schema::table('fichajes', function (Blueprint $table) {
            $table->dropColumn('work_mode');
        });
    }
};
