<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('cedula_rnc', 20)->unique(); // used as username
            $table->string('telefono', 20);
            $table->string('password');
            $table->string('codigo_referido', 20)->unique(); // generated referral code
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('primera_vez')->default(true); // track first login
            $table->timestamps();
        });

        Schema::create('referente_datos_bancarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referente_id')->constrained('referentes')->onDelete('cascade');
            $table->enum('banco', ['Popular', 'BHD', 'Banreservas', 'Banesco', 'Qik', 'Promerica']);
            $table->enum('tipo_cuenta', ['Corriente', 'Ahorro']);
            $table->string('numero_cuenta', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referente_datos_bancarios');
        Schema::dropIfExists('referentes');
    }
};
