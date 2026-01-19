<?php

namespace Database\Seeders;

use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PrestasiSeeder extends Seeder
{
    private function downloadImage($url, $filename): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || $contents === false) {
            $this->command->warn("Failed to download: {$filename}");
            return null;
        }
        
        $path = 'prestasi/' . $filename;
        Storage::disk('public')->put($path, $contents);
        $this->command->info("Downloaded: {$filename}");
        
        return $path;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table for idempotency
        Prestasi::truncate();

        // Get admin user for user_id
        $admin = User::where('role', 'admin')->first();

        $prestasis = [
            [
                'judul' => 'Juara 1 Lomba Kompetensi Siswa Bidang IT Software Solution',
                'deskripsi' => 'Tim siswa RPL berhasil meraih juara pertama dalam kompetisi pengembangan software solution tingkat nasional dengan mengembangkan aplikasi manajemen sekolah berbasis cloud.',
                'jenis' => 'kompetisi',
                'tingkat' => 'nasional',
                'penyelenggara' => 'Kementerian Pendidikan dan Kebudayaan',
                'tanggal_prestasi' => '2025-11-15',
                'nama_siswa' => 'Ahmad Fauzi, Siti Nurhaliza, Budi Santoso',
                'kelas' => 'XII RPL 1',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-lks/1200/800', 'prestasi-lks.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Juara 2 Hackathon Nasional Smart City Solutions',
                'deskripsi' => 'Meraih runner-up dalam kompetisi hackathon dengan mengembangkan prototype sistem manajemen transportasi umum berbasis IoT dan AI dalam waktu 48 jam.',
                'jenis' => 'kompetisi',
                'tingkat' => 'nasional',
                'penyelenggara' => 'Indonesia Digital Innovation',
                'tanggal_prestasi' => '2025-10-20',
                'nama_siswa' => 'Rizky Pratama, Dewi Lestari',
                'kelas' => 'XII RPL 2',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-hackathon/1200/800', 'prestasi-hackathon.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Sertifikasi Junior Web Developer',
                'deskripsi' => 'Berhasil mendapatkan sertifikasi kompetensi Junior Web Developer dari BNSP dengan nilai sangat memuaskan, menguasai HTML, CSS, JavaScript, PHP, dan framework Laravel.',
                'jenis' => 'sertifikasi',
                'tingkat' => 'nasional',
                'penyelenggara' => 'Badan Nasional Sertifikasi Profesi (BNSP)',
                'tanggal_prestasi' => '2025-09-10',
                'nama_siswa' => '15 Siswa Kelas XII RPL',
                'kelas' => 'XII RPL 1 & 2',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-bnsp/1200/800', 'prestasi-bnsp.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Juara 3 Olimpiade IT Tingkat Provinsi',
                'deskripsi' => 'Meraih medali perunggu dalam Olimpiade Teknologi Informasi tingkat provinsi dengan menyelesaikan soal programming logic dan algoritma kompleks.',
                'jenis' => 'akademik',
                'tingkat' => 'provinsi',
                'penyelenggara' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'tanggal_prestasi' => '2025-08-05',
                'nama_siswa' => 'Maya Anggraini',
                'kelas' => 'XII RPL 2',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-olimpiade/1200/800', 'prestasi-olimpiade.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Best Project Award Tech Expo 2025',
                'deskripsi' => 'Proyek Sistem Informasi Geografis (GIS) untuk pemetaan potensi wisata daerah mendapat penghargaan Best Project di ajang pameran teknologi sekolah.',
                'jenis' => 'non-akademik',
                'tingkat' => 'sekolah',
                'penyelenggara' => 'OSIS SMK',
                'tanggal_prestasi' => '2025-07-12',
                'nama_siswa' => 'Tim RPL Kelas XI',
                'kelas' => 'XI RPL 1',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-expo/1200/800', 'prestasi-expo.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Finalis Mobile App Development Competition',
                'deskripsi' => 'Lolos sebagai 10 finalis terbaik dari 150 peserta dalam kompetisi pengembangan aplikasi mobile dengan tema Solusi Pendidikan Digital.',
                'jenis' => 'kompetisi',
                'tingkat' => 'nasional',
                'penyelenggara' => 'Google Developer Student Clubs Indonesia',
                'tanggal_prestasi' => '2025-06-18',
                'nama_siswa' => 'Budi Santoso, Rizky Pratama',
                'kelas' => 'XII RPL 1',
                'tahun' => 2025,
                'gambar' => $this->downloadImage('https://picsum.photos/seed/prestasi-mobile/1200/800', 'prestasi-mobile.jpg'),
                'sertifikat' => null,
                'status' => 'published',
                'user_id' => $admin->id,
            ],
        ];

        foreach ($prestasis as $prestasi) {
            Prestasi::create($prestasi);
        }

        $this->command->info('✓ Prestasi seeded successfully (6 records)');
    }
}
