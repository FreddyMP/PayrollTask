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
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type'); // image, video
            $table->enum('uploader_group', ['supervisor', 'assigned']);
            $table->timestamps();
        });

        // Drop the old single attachment field from tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('due_date');
        });
    }
};
