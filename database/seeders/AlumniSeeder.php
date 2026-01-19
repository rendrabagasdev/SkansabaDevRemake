<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AlumniSeeder extends Seeder
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
        
        $path = 'alumni/' . $filename;
        Storage::disk('public')->put($path, $contents);
        $this->command->info("Downloaded: {$filename}");
        
        return $path;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@rpl.sch.id')->first();

        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping Alumni seeder.');
            return;
        }

        $alumniData = [
            [
                'nama' => 'Ahmad Fauzi',
                'tahun_lulus' => 2023,
                'status_alumni' => 'kuliah',
                'institusi' => 'Institut Teknologi Bandung',
                'bidang' => 'Teknik Informatika',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=11', 'alumni-ahmad-fauzi.jpg'),
                'deskripsi' => "## Profil Singkat\n\nMelanjutkan kuliah di ITB dengan beasiswa prestasi.\n\n**Pencapaian:**\n- Beasiswa penuh ITB\n- Aktif di komunitas open source\n- Fokus di bidang AI dan Machine Learning",
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'tahun_lulus' => 2023,
                'status_alumni' => 'kerja',
                'institusi' => 'PT Gojek Indonesia',
                'bidang' => 'Frontend Developer',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=27', 'alumni-siti-nurhaliza.jpg'),
                'deskripsi' => "## Pengalaman Kerja\n\nBergabung dengan Gojek sebagai Frontend Developer sejak lulus.\n\n**Tech Stack:**\n- React.js & Next.js\n- TypeScript\n- Tailwind CSS",
                'status' => 'published',
                'published_at' => now()->subDays(9),
            ],
            [
                'nama' => 'Budi Santoso',
                'tahun_lulus' => 2022,
                'status_alumni' => 'wirausaha',
                'institusi' => 'TechStart Solutions',
                'bidang' => 'Software Development',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=13', 'alumni-budi-santoso.jpg'),
                'deskripsi' => "## Perjalanan Wirausaha\n\nMendirikan startup di bidang software development.\n\n**Fokus Bisnis:**\n- Web & Mobile App Development\n- IT Consulting\n- Cloud Solutions",
                'status' => 'published',
                'published_at' => now()->subDays(8),
            ],
            [
                'nama' => 'Dewi Lestari',
                'tahun_lulus' => 2024,
                'status_alumni' => 'kuliah',
                'institusi' => 'Universitas Indonesia',
                'bidang' => 'Sistem Informasi',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=44', 'alumni-dewi-lestari.jpg'),
                'deskripsi' => "## Pengalaman Akademis\n\nSaat ini sedang menempuh S1 Sistem Informasi di UI.\n\n**Aktivitas:**\n- Anggota BEM Fakultas\n- Asisten praktikum programming\n- Peserta hackathon regional",
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ],
            [
                'nama' => 'Rizki Pratama',
                'tahun_lulus' => 2022,
                'status_alumni' => 'kerja',
                'institusi' => 'Tokopedia',
                'bidang' => 'Backend Engineer',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=14', 'alumni-rizki-pratama.jpg'),
                'deskripsi' => "## Karir di Tech Industry\n\nBekerja sebagai Backend Engineer di Tokopedia.\n\n**Expertise:**\n- Go Lang\n- Microservices Architecture\n- Docker & Kubernetes",
                'status' => 'published',
                'published_at' => now()->subDays(6),
            ],
            [
                'nama' => 'Eka Putri',
                'tahun_lulus' => 2021,
                'status_alumni' => 'kerja',
                'institusi' => 'Bank Central Asia',
                'bidang' => 'IT Security Analyst',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=48', 'alumni-eka-putri.jpg'),
                'deskripsi' => "## Karir di Perbankan\n\nBergabung dengan BCA sebagai IT Security Analyst.\n\n**Fokus Pekerjaan:**\n- Cybersecurity\n- Network Security\n- Penetration Testing",
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'nama' => 'Arif Hidayat',
                'tahun_lulus' => 2023,
                'status_alumni' => 'wirausaha',
                'institusi' => 'Kreasi Digital Studio',
                'bidang' => 'Digital Marketing & Design',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=52', 'alumni-arif-hidayat.jpg'),
                'deskripsi' => "## Membangun Bisnis Kreatif\n\nMendirikan studio kreatif yang fokus di digital marketing.\n\n**Layanan:**\n- Social Media Management\n- Graphic Design\n- Website Development",
                'status' => 'published',
                'published_at' => now()->subDays(4),
            ],
            [
                'nama' => 'Andi Wijaya',
                'tahun_lulus' => 2024,
                'status_alumni' => 'belum_diketahui',
                'institusi' => null,
                'bidang' => null,
                'deskripsi' => null,
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($alumniData as $data) {
            Alumni::create(array_merge($data, [
                'user_id' => $admin->id,
            ]));
        }

        $this->command->info('✅ Alumni seeded successfully (8 records)!');
    }
}
