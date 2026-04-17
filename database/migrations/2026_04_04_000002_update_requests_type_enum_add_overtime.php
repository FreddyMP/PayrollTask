<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SQLite no permite modificar CHECK constraints (generados por enum) con ALTER TABLE.
 * La solución es recrear la tabla con el enum actualizado y copiar los datos.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla temporal con el enum actualizado
        Schema::create('requests_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->enum('type', ['vacation', 'permission', 'work_letter', 'overtime'])->default('vacation');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            // Columnas de horas extra (añadidas en migración anterior)
            $table->date('overtime_date')->nullable();
            $table->time('overtime_start')->nullable();
            $table->time('overtime_end')->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamps();
        });

        // 2. Copiar los datos existentes
        DB::statement('INSERT INTO requests_new SELECT * FROM requests');

        // 3. Eliminar la tabla original
        Schema::drop('requests');

        // 4. Renombrar la nueva tabla
        Schema::rename('requests_new', 'requests');
    }

    public function down(): void
    {
        // Revertir: recrear sin 'overtime'
        Schema::create('requests_old', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->enum('type', ['vacation', 'permission', 'work_letter'])->default('vacation');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->date('overtime_date')->nullable();
            $table->time('overtime_start')->nullable();
            $table->time('overtime_end')->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamps();
        });

        DB::statement("INSERT INTO requests_old SELECT * FROM requests WHERE type != 'overtime'");
        Schema::drop('requests');
        Schema::rename('requests_old', 'requests');
    }
};
