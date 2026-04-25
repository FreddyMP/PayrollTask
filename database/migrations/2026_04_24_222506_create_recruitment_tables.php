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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('department')->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
        });

        Schema::create('recruitment_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('responsible_id')->constrained('users');
            $table->integer('points')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('status')->default('active'); // active, discarded, hired
            $table->timestamps();
        });

        Schema::create('candidate_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('recruitment_step_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->string('status')->default('pending'); // pending, completed, discarded
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_progress');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('recruitment_steps');
        Schema::dropIfExists('vacancies');
    }
};
