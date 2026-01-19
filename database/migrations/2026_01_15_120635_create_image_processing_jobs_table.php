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
        Schema::create('image_processing_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('temp_path');
            $table->string('storage_path');
            $table->json('options')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('output_path')->nullable();
            $table->string('fallback_path')->nullable();
            $table->boolean('fallback_used')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_processing_jobs');
    }
};
