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
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('jenis', ['akademik', 'non-akademik', 'kompetisi', 'sertifikasi']);
            $table->enum('tingkat', ['sekolah', 'kecamatan', 'kota', 'provinsi', 'nasional', 'internasional']);
            $table->string('penyelenggara');
            $table->string('nama_siswa');
            $table->string('kelas');
            $table->date('tanggal_prestasi');
            $table->year('tahun');
            $table->string('gambar')->nullable();
            $table->string('sertifikat')->nullable();
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index(['jenis', 'tingkat']);
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
