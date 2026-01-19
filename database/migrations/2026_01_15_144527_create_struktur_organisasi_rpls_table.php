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
        Schema::create('struktur_organisasi_rpls', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama lengkap
            $table->string('jabatan'); // Jabatan/posisi
            $table->string('foto'); // MANDATORY: 1:1 aspect ratio locked, processed via ImageService
            $table->text('deskripsi_md')->nullable(); // MARKDOWN format, processed via MarkdownService
            $table->integer('order')->default(0); // For drag & drop ordering
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
            
            $table->index(['status', 'order']); // Query optimization
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struktur_organisasi_rpls');
    }
};
