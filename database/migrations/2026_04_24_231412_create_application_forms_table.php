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
        Schema::create('application_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('application_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_form_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->string('type'); // text, long_text, textarea, date, integer, decimal, table
            $table->json('options')->nullable(); // columns for table
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_form_fields');
        Schema::dropIfExists('application_forms');
    }
};
