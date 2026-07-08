<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fichajes', function (Blueprint $table) {
            // Rename existing latitude/longitude to clarify they belong to clock_in
            $table->renameColumn('latitude', 'clock_in_latitude');
            $table->renameColumn('longitude', 'clock_in_longitude');

            // Add clock_out location columns
            $table->decimal('clock_out_latitude', 10, 7)->nullable()->after('clock_in_longitude');
            $table->decimal('clock_out_longitude', 10, 7)->nullable()->after('clock_out_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('fichajes', function (Blueprint $table) {
            $table->dropColumn(['clock_out_latitude', 'clock_out_longitude']);
            $table->renameColumn('clock_in_latitude', 'latitude');
            $table->renameColumn('clock_in_longitude', 'longitude');
        });
    }
};
