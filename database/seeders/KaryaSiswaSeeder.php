<?php

namespace Database\Seeders;

use App\Models\KaryaSiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class KaryaSiswaSeeder extends Seeder
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
            
            $path = 'karya-siswa/' . $filename;
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
        KaryaSiswa::truncate();

        // Get admin user for user_id
        $admin = User::where('role', 'admin')->first();

        $karyaSiswas = [
            [
                'judul' => 'Sistem Informasi Perpustakaan Digital',
                'deskripsi' => 'Aplikasi web untuk mengelola peminjaman buku, katalog digital, dan sistem keanggotaan perpustakaan sekolah dengan fitur notifikasi otomatis.',
                'kategori' => 'web',
                'teknologi' => 'Laravel, MySQL, Bootstrap, jQuery',
                'nama_siswa' => 'Ahmad Fauzi',
                'kelas' => 'XII RPL 1',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/library-system/800/600', 'library-system.jpg'),
                'url_demo' => 'https://perpus-digital.smkrpl.sch.id',
                'url_repo' => 'https://github.com/smkrpl/perpus-digital',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Aplikasi Kasir Toko Serbaguna',
                'deskripsi' => 'Aplikasi desktop untuk sistem point of sale (POS) dengan fitur manajemen stok, laporan penjualan, dan integrasi printer thermal.',
                'kategori' => 'desktop',
                'teknologi' => 'C#, .NET Framework, SQL Server',
                'nama_siswa' => 'Siti Nurhaliza',
                'kelas' => 'XII RPL 2',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/pos-system/800/600', 'pos-system.jpg'),
                'url_demo' => null,
                'url_repo' => 'https://github.com/smkrpl/kasir-toko',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Mobile E-Learning Platform',
                'deskripsi' => 'Aplikasi mobile untuk pembelajaran online dengan fitur video course, quiz interaktif, progress tracking, dan sertifikat digital.',
                'kategori' => 'mobile',
                'teknologi' => 'Flutter, Firebase, Dart',
                'nama_siswa' => 'Budi Santoso',
                'kelas' => 'XII RPL 1',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/elearning-mobile/800/600', 'elearning-mobile.jpg'),
                'url_demo' => null,
                'url_repo' => 'https://github.com/smkrpl/elearning-mobile',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Sistem Absensi Siswa Berbasis QR Code',
                'deskripsi' => 'Aplikasi web dan mobile untuk absensi siswa menggunakan QR Code dengan dashboard real-time dan notifikasi ke orang tua.',
                'kategori' => 'web',
                'teknologi' => 'Laravel, Vue.js, MySQL, Firebase',
                'nama_siswa' => 'Dewi Lestari',
                'kelas' => 'XII RPL 2',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/qrcode-attendance/800/600', 'qrcode-attendance.jpg'),
                'url_demo' => 'https://absensi.smkrpl.sch.id',
                'url_repo' => 'https://github.com/smkrpl/absensi-qrcode',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Smart Parking System',
                'deskripsi' => 'Sistem parkir pintar dengan sensor IoT, aplikasi mobile untuk mencari tempat parkir kosong, dan payment gateway terintegrasi.',
                'kategori' => 'iot',
                'teknologi' => 'Arduino, Node.js, React Native, MQTT',
                'nama_siswa' => 'Rizky Pratama',
                'kelas' => 'XII RPL 1',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/smart-parking/800/600', 'smart-parking.jpg'),
                'url_demo' => null,
                'url_repo' => 'https://github.com/smkrpl/smart-parking',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
            [
                'judul' => 'Website Portofolio Interaktif',
                'deskripsi' => 'Template website portofolio modern dengan animasi 3D, dark mode, dan CMS headless untuk memudahkan update konten.',
                'kategori' => 'web',
                'teknologi' => 'Next.js, Three.js, Tailwind CSS, Strapi',
                'nama_siswa' => 'Maya Anggraini',
                'kelas' => 'XII RPL 2',
                'tahun' => '2025',
                'gambar' => $this->downloadImage('https://picsum.photos/seed/portfolio-website/800/600', 'portfolio-website.jpg'),
                'url_demo' => 'https://portofolio-maya.vercel.app',
                'url_repo' => 'https://github.com/smkrpl/portfolio-3d',
                'status' => 'published',
                'user_id' => $admin->id,
            ],
        ];

        foreach ($karyaSiswas as $karyaSiswa) {
            KaryaSiswa::create($karyaSiswa);
        }

        $this->command->info('✓ Karya siswa seeded successfully (6 records)');
    }
}
