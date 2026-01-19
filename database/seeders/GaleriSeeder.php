<?php

namespace Database\Seeders;

use App\Models\Galeri;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GaleriSeeder extends Seeder
{
    /**
     * Download image from URL and save to storage
     */
    private function downloadImage($url, $filename): ?string
    {
        try {
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
            
            $path = 'galeri/' . $filename;
            Storage::disk('public')->put($path, $contents);
            $this->command->info("Downloaded: {$filename}");
            return $path;
        } catch (\Exception $e) {
            $this->command->error("Error downloading {$filename}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table for idempotency
        Galeri::truncate();

        // Get admin user for user_id
        $admin = User::where('role', 'admin')->first();

        $galeris = [
            [
                'judul' => 'Praktikum Web Programming di Lab Komputer',
                'deskripsi' => 'Siswa-siswi kelas XI RPL sedang mengikuti praktikum pengembangan website menggunakan framework Laravel dengan pembimbing guru profesional.',
                'kategori' => 'pembelajaran',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/web-programming/1200/800', 'web-programming.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Laboratorium Komputer Modern dengan 40 Unit PC',
                'deskripsi' => 'Fasilitas laboratorium komputer dilengkapi dengan perangkat spesifikasi tinggi, monitor LED, dan software development tools terkini untuk mendukung pembelajaran.',
                'kategori' => 'pembelajaran',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/computer-lab/1200/800', 'computer-lab.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Penyerahan Piala Juara 1 LKS IT Software Solution',
                'deskripsi' => 'Momen bersejarah penyerahan piala juara pertama lomba kompetensi siswa bidang IT Software Solution tingkat nasional kepada tim RPL kami.',
                'kategori' => 'lomba',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/award-ceremony/1200/800', 'award-ceremony.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Workshop Mobile Development dengan Flutter',
                'deskripsi' => 'Kegiatan workshop pengembangan aplikasi mobile menggunakan Flutter yang diikuti oleh 50 siswa dengan instruktur dari industri teknologi.',
                'kategori' => 'kegiatan',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/flutter-workshop/1200/800', 'flutter-workshop.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Tech Expo 2025 - Pameran Karya Siswa RPL',
                'deskripsi' => 'Pameran tahunan karya inovatif siswa jurusan RPL yang menampilkan berbagai proyek aplikasi, website, dan sistem informasi yang telah dikembangkan.',
                'kategori' => 'kegiatan',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/tech-expo/1200/800', 'tech-expo.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Kunjungan Industri ke Startup Technology Hub',
                'deskripsi' => 'Siswa kelas XII RPL mengunjungi perusahaan startup teknologi untuk melihat langsung proses pengembangan produk digital dan berinteraksi dengan developer profesional.',
                'kategori' => 'kunjungan',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/industry-visit/1200/800', 'industry-visit.jpg'),
                'status' => 'published',
                'user_id' => $admin->id,
            ],
        ];

        foreach ($galeris as $galeri) {
            Galeri::create($galeri);
        }

        $this->command->info('✓ Galeri seeded successfully (6 records)');
    }
}
