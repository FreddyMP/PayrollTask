<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('department')->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->date('hire_date')->nullable();
            $table->enum('contract_type', ['full_time', 'part_time', 'contractor'])->default('full_time');
            $table->string('bank_account')->nullable();
            $table->string('id_number', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
