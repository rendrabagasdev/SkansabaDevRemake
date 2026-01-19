<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StrukturOrganisasiRpl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiRplSeeder extends Seeder
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
            
            $path = 'struktur-organisasi/' . $filename;
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
        // Truncate table
        DB::table('struktur_organisasi_rpls')->truncate();

        $strukturList = [
            [
                'nama' => 'Drs. Ahmad Subakir, M.Pd',
                'jabatan' => 'Kepala Jurusan RPL',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=12', 'kepala-jurusan.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nPengalaman 15 tahun di bidang pendidikan teknologi. Aktif mengembangkan kurikulum berbasis industri.\n\n**Kompetensi:**\n- Manajemen Pendidikan\n- Pengembangan Kurikulum\n- Kemitraan Industri",
                'order' => 0,
                'status' => 'published',
            ],
            [
                'nama' => 'Siti Maryam, S.Kom, M.T',
                'jabatan' => 'Wakil Kepala Jurusan',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=5', 'wakil-kajur.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nKoordinator Kurikulum & Pembelajaran. Spesialisasi Software Engineering Education.\n\n**Bidang Keahlian:**\n- Software Engineering\n- Desain Kurikulum\n- Asesmen Pembelajaran",
                'order' => 1,
                'status' => 'published',
            ],
            [
                'nama' => 'Budi Santoso, S.Kom',
                'jabatan' => 'Guru Pengampu Web Development',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=15', 'guru-web.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nMengampu mata pelajaran Web Programming dan Framework.\n\n**Teknologi yang Dikuasai:**\n- Laravel Framework\n- Vue.js & React\n- RESTful API Design",
                'order' => 2,
                'status' => 'published',
            ],
            [
                'nama' => 'Rina Wulandari, S.Kom',
                'jabatan' => 'Guru Pengampu Mobile Development',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=9', 'guru-mobile.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nSpesialis pengembangan aplikasi mobile native dan hybrid.\n\n**Teknologi:**\n- Flutter\n- React Native\n- Android Native (Kotlin)",
                'order' => 3,
                'status' => 'published',
            ],
            [
                'nama' => 'Agus Prasetyo, S.T, M.Kom',
                'jabatan' => 'Guru Pengampu Database & Backend',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=33', 'guru-backend.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nAhli database dan backend development dengan pengalaman industri 8 tahun.\n\n**Keahlian:**\n- MySQL, PostgreSQL\n- Node.js Backend\n- Microservices Architecture",
                'order' => 4,
                'status' => 'published',
            ],
            [
                'nama' => 'Dewi Lestari, S.Kom',
                'jabatan' => 'Guru Pengampu UI/UX Design',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=47', 'guru-uiux.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nDesainer UI/UX dengan fokus pada user-centered design.\n\n**Tools & Metodologi:**\n- Figma, Adobe XD\n- Design Thinking\n- Usability Testing",
                'order' => 5,
                'status' => 'published',
            ],
            [
                'nama' => 'Joko Widodo, S.Kom',
                'jabatan' => 'Teknisi Laboratorium',
                'foto' => $this->downloadImage('https://i.pravatar.cc/400?img=51', 'teknisi-lab.jpg'),
                'deskripsi_md' => "## Profil Singkat\n\nMengelola infrastruktur jaringan dan perawatan laboratorium komputer.\n\n**Tanggung Jawab:**\n- Maintenance Hardware\n- Network Administration\n- Server Management",
                'order' => 6,
                'status' => 'published',
            ],
        ];

        foreach ($strukturList as $item) {
            StrukturOrganisasiRpl::create($item);
        }

        $this->command->info('✓ Struktur Organisasi RPL seeded successfully (' . count($strukturList) . ' records)');
    }
}
