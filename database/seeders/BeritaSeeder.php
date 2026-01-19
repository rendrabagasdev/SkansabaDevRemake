<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BeritaSeeder extends Seeder
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
        
        $path = 'berita/' . $filename;
        Storage::disk('public')->put($path, $contents);
        $this->command->info("Downloaded: {$filename}");
        
        return $path;
    }

    public function run(): void
    {
        $this->command->info('📰 Seeding berita...');

        Berita::truncate();

        $admin = User::where('role', 'admin')->first();
        $guru = User::where('role', 'guru')->first();

        // Create default users if they don't exist
        if (!$admin) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@rpl.sch.id'],
                [
                    'name' => 'Administrator',
                    'password' => \Hash::make('admin123'),
                    'role' => 'admin',
                    'status' => 'active',
                ]
            );
        }

        if (!$guru) {
            $guru = User::firstOrCreate(
                ['email' => 'guru@rpl.sch.id'],
                [
                    'name' => 'Guru RPL',
                    'password' => \Hash::make('guru123'),
                    'role' => 'guru',
                    'status' => 'active',
                ]
            );
        }

        $berita = [
            [
                'title' => 'Siswa RPL Juara 1 Lomba Web Development Tingkat Provinsi',
                'excerpt' => 'Tim siswa RPL berhasil meraih juara pertama dalam lomba web development yang diselenggarakan oleh Dinas Pendidikan Provinsi.',
                'content_md' => "# Prestasi Membanggakan Siswa RPL\n\nTim siswa Rekayasa Perangkat Lunak (RPL) kembali mengharumkan nama sekolah dengan meraih **juara pertama** dalam Lomba Web Development Tingkat Provinsi yang diselenggarakan pada 15 Januari 2026.\n\n## Detail Kompetisi\n\nKompetisi yang berlangsung selama 2 hari ini diikuti oleh 45 tim dari berbagai sekolah di seluruh provinsi. Para peserta diminta untuk membuat website e-commerce lengkap dengan fitur:\n\n- Sistem autentikasi dan otorisasi\n- Katalog produk dengan filter dan pencarian\n- Keranjang belanja dan checkout\n- Dashboard admin untuk manajemen produk\n- Responsive design untuk mobile dan desktop\n\n## Tim Pemenang\n\nTim yang beranggotakan:\n- Ahmad Fauzi (Ketua Tim) - Kelas XII RPL 1\n- Siti Nurhaliza - Kelas XII RPL 1\n- Budi Santoso - Kelas XI RPL 2\n\nberhasil mengembangkan website dengan teknologi:\n- **Frontend**: React.js dengan Tailwind CSS\n- **Backend**: Laravel 11\n- **Database**: MySQL\n- **Deployment**: Vercel dan Railway\n\n## Kata Kepala Jurusan\n\n\"Kami sangat bangga dengan prestasi yang diraih siswa-siswi kami. Ini membuktikan bahwa kurikulum dan metode pembelajaran yang kami terapkan mampu menghasilkan lulusan yang kompeten dan siap bersaing,\" ujar Pak Agus Setiawan, S.Kom., M.T., Kepala Jurusan RPL.\n\n## Hadiah dan Penghargaan\n\nTim pemenang membawa pulang:\n- Piala Bergilir Gubernur\n- Uang pembinaan Rp 10.000.000\n- Sertifikat juara\n- Kesempatan magang di perusahaan IT ternama\n\nSelamat kepada para juara! Semoga prestasi ini dapat memotivasi siswa lainnya untuk terus belajar dan berprestasi.",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-lomba-web/1200/800', 'news-lomba-web.jpg'),
                'author_id' => $admin->id,
                'published_at' => now()->subDays(2),
                'status' => 'published',
                'is_highlight' => true,
                'views' => 234,
            ],
            [
                'title' => 'Workshop Mobile App Development dengan Flutter',
                'excerpt' => 'Jurusan RPL mengadakan workshop pengembangan aplikasi mobile menggunakan Flutter yang diikuti oleh 60 siswa.',
                'content_md' => "# Workshop Flutter: Membangun Aplikasi Mobile Modern\n\nJurusan RPL sukses mengadakan workshop pengembangan aplikasi mobile menggunakan **Flutter** pada 10-12 Januari 2026. Workshop ini diikuti oleh 60 siswa dari kelas X, XI, dan XII RPL.\n\n## Materi Workshop\n\nWorkshop dibagi menjadi 3 sesi:\n\n### Hari 1: Fundamentals\n- Pengenalan Flutter dan Dart\n- Setup development environment\n- Widget dasar dan layout\n- Stateless vs Stateful Widget\n\n### Hari 2: Advanced Topics\n- State management dengan Provider\n- Navigation dan routing\n- HTTP request dan REST API\n- Local storage dengan SharedPreferences\n\n### Hari 3: Project\n- Membuat aplikasi Todo List lengkap\n- Integration dengan backend\n- Build APK untuk Android\n- Publishing preparation\n\n## Narasumber\n\nWorkshop diisi oleh:\n- **Bapak Rizki Pratama, S.Kom.** - Mobile Developer di startup unicorn\n- **Ibu Dewi Sartika, S.Kom., M.T.** - Guru produktif RPL\n\n## Hasil Workshop\n\nSetiap peserta berhasil membuat aplikasi Todo List dengan fitur:\n- Tambah, edit, hapus task\n- Filter task by status\n- Dark mode\n- Offline first dengan local storage\n\n## Testimoni Siswa\n\n*\"Workshop ini sangat bermanfaat! Saya yang tadinya hanya bisa web development sekarang jadi bisa bikin aplikasi mobile juga.\"* - Rina Putri, Kelas XI RPL 1\n\n*\"Penjelasan narasumber sangat jelas dan hands-on. Dalam 3 hari saya sudah bisa deploy aplikasi ke HP saya sendiri!\"* - Fahmi Hakim, Kelas XII RPL 2\n\n## Rencana Kedepan\n\nSekolah berencana mengadakan workshop lanjutan dengan topik:\n- State management advanced (Bloc, Riverpod)\n- Firebase integration\n- Publishing ke Google Play Store\n- UI/UX design untuk mobile app",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-workshop-flutter/1200/800', 'news-workshop-flutter.jpg'),
                'author_id' => $guru->id,
                'published_at' => now()->subDays(7),
                'status' => 'published',
                'is_highlight' => true,
                'views' => 189,
            ],
            [
                'title' => 'Kunjungan Industri ke Perusahaan IT Terkemuka',
                'excerpt' => 'Siswa kelas XII RPL melakukan kunjungan industri ke salah satu perusahaan IT terkemuka di Indonesia untuk melihat langsung dunia kerja profesional.',
                'content_md' => "# Kunjungan Industri: Menjembatani Teori dan Praktik\n\nSiswa kelas XII RPL melakukan kunjungan industri ke **PT. Teknologi Nusantara** pada 5 Januari 2026. Kunjungan ini bertujuan untuk memberikan gambaran nyata tentang dunia kerja profesional di bidang IT.\n\n## Rundown Kegiatan\n\n### 09.00 - 10.00: Company Tour\nSiswa diajak berkeliling kantor modern seluas 5000m² dengan fasilitas:\n- Co-working space dengan desain aesthetic\n- Meeting room dengan smart technology\n- Gaming room untuk break time\n- Cafeteria dengan menu gratis\n- Gym dan yoga studio\n\n### 10.00 - 11.30: Tech Talk\nSesi sharing dari:\n- **Head of Engineering**: Teknologi dan stack yang digunakan\n- **Senior Developer**: Career path di industri IT\n- **HR Manager**: Tips lolos interview dan soft skills yang dibutuhkan\n\n### 11.30 - 13.00: Live Coding Session\nDemonstrasi development workflow:\n- Git collaboration\n- Code review process\n- Testing dan deployment\n- Agile development methodology\n\n### 13.00 - 14.00: Q&A Session\nSiswa aktif bertanya tentang:\n- Teknologi yang harus dipelajari\n- Gaji dan benefit di industri IT\n- Work-life balance\n- Peluang magang dan fresh graduate\n\n## Insight Berharga\n\n### Teknologi yang Banyak Digunakan\n- **Backend**: Node.js, Python (Django/FastAPI), Go\n- **Frontend**: React, Vue.js, Next.js\n- **Mobile**: Flutter, React Native\n- **Database**: PostgreSQL, MongoDB, Redis\n- **DevOps**: Docker, Kubernetes, CI/CD\n\n### Soft Skills yang Penting\n- Problem solving\n- Communication dan teamwork\n- Time management\n- Adaptability dengan teknologi baru\n- English proficiency\n\n## Testimoni Siswa\n\n*\"Kantor nya keren banget! Bikin semangat untuk kuliah dan kerja di perusahaan IT.\"* - Dimas Prakoso\n\n*\"Sekarang jadi tahu teknologi apa yang harus dipelajari dan skill apa yang perlu dikuasai.\"* - Sarah Amelia\n\n## Follow Up\n\nPerusahaan menawarkan:\n- Program magang untuk siswa kelas XII\n- Beasiswa kuliah untuk 2 siswa berprestasi\n- Mentoring program\n- Prioritas recruitment untuk lulusan terbaik",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-kunjungan-industri/1200/800', 'news-kunjungan-industri.jpg'),
                'author_id' => $admin->id,
                'published_at' => now()->subDays(14),
                'status' => 'published',
                'is_highlight' => false,
                'views' => 156,
            ],
            [
                'title' => 'Peluncuran Lab Komputer Baru dengan Spesifikasi High-End',
                'excerpt' => 'Sekolah meresmikan lab komputer baru dengan 40 unit PC gaming spec untuk mendukung pembelajaran programming dan desain.',
                'content_md' => "# Lab Komputer Baru: Fasilitas Kelas Dunia untuk Siswa RPL\n\nSekolah resmi meluncurkan **Lab Komputer RPL 2.0** pada 1 Januari 2026. Lab ini dilengkapi dengan 40 unit komputer spesifikasi tinggi untuk mendukung pembelajaran yang lebih optimal.\n\n## Spesifikasi Komputer\n\nSetiap unit dilengkapi dengan:\n- **Processor**: Intel Core i7 Gen 13 / AMD Ryzen 7\n- **RAM**: 32GB DDR5\n- **Storage**: 1TB NVMe SSD\n- **GPU**: NVIDIA RTX 4060 8GB\n- **Monitor**: 27 inch 144Hz IPS\n- **Keyboard & Mouse**: Mechanical keyboard RGB + gaming mouse\n- **Headset**: Audio technica untuk pembelajaran multimedia\n\n## Fasilitas Pendukung\n\n### Network Infrastructure\n- Dedicated internet 1 Gbps\n- Wi-Fi 6E access point\n- Managed switch dengan VLAN segregation\n- Local server untuk version control (GitLab)\n\n### Software Licensed\n- Windows 11 Pro\n- Microsoft Office 365\n- Visual Studio Professional\n- JetBrains All Products Pack\n- Adobe Creative Cloud\n- Figma Education License\n\n### Furniture & Interior\n- Meja dan kursi ergonomis\n- Adjustable desk untuk standing work\n- Sound dampening panels\n- RGB ambient lighting\n- AC dengan temperature control\n\n## Kegunaan\n\nLab ini akan digunakan untuk:\n\n### Programming & Development\n- Web development (Frontend & Backend)\n- Mobile app development\n- Desktop application\n- Game development\n- AI/ML projects\n\n### Design & Multimedia\n- UI/UX design\n- Graphic design\n- Video editing\n- 3D modeling\n- Animation\n\n### Testing & Deployment\n- Application testing\n- Performance testing\n- Cross-browser testing\n- Deployment simulation\n\n## Kata Kepala Sekolah\n\n*\"Investasi di bidang pendidikan adalah investasi untuk masa depan bangsa. Dengan fasilitas yang memadai, kami yakin siswa-siswi RPL dapat berkompetisi di level nasional bahkan internasional.\"* - Bapak Drs. Bambang Sutrisno, M.Pd.\n\n## Jadwal Penggunaan\n\n- Senin - Jumat: 07.00 - 17.00 (jam pelajaran)\n- Sabtu: 08.00 - 14.00 (ekstrakurikuler)\n- Minggu: 08.00 - 12.00 (project pribadi dengan booking)\n\n## Aturan Penggunaan\n\n1. Wajib mengisi buku log book\n2. Jaga kebersihan dan ketertiban\n3. Tidak boleh install software tanpa izin\n4. Tidak boleh mengakses konten ilegal\n5. Bertanggung jawab atas equipment yang digunakan\n\nDengan fasilitas baru ini, diharapkan kualitas pembelajaran dan karya siswa RPL semakin meningkat!",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-lab-baru/1200/800', 'news-lab-baru.jpg'),
                'author_id' => $admin->id,
                'published_at' => now()->subDays(18),
                'status' => 'published',
                'is_highlight' => false,
                'views' => 312,
            ],
            [
                'title' => 'Kerja Sama dengan Universitas untuk Program Dual Degree',
                'excerpt' => 'Sekolah menjalin kerja sama dengan universitas terkemuka untuk program dual degree bagi lulusan RPL.',
                'content_md' => "# Program Dual Degree: Jalur Cepat Menuju Sarjana IT\n\nSekolah resmi menandatangani MoU dengan **Universitas Teknologi Indonesia** untuk program dual degree pada 20 Desember 2025. Program ini memberikan kesempatan bagi lulusan RPL untuk menempuh pendidikan S1 dengan durasi lebih singkat.\n\n## Keunggulan Program\n\n### Credit Transfer\n- 30 SKS diakui dari pembelajaran SMK\n- Mata kuliah programming, database, dan web development\n- Pengurangan masa studi 1-2 semester\n- Fokus ke mata kuliah advanced\n\n### Fast Track\n- Lulus S1 dalam 3 - 3.5 tahun\n- Hemat biaya kuliah\n- Lebih cepat masuk dunia kerja\n- Kompetitif di job market\n\n### Beasiswa\nUniversitas menyediakan:\n- Beasiswa 100% untuk 3 siswa terbaik\n- Beasiswa 50% untuk 10 siswa berprestasi\n- Beasiswa parsial untuk siswa berprestasi akademik\n\n## Syarat Program\n\n### Akademik\n- Rata-rata nilai rapor minimal 80\n- Nilai mata pelajaran produktif minimal 85\n- Portfolio project yang baik\n- Lulus ujian masuk universitas\n\n### Non-Akademik\n- Surat rekomendasi dari kepala jurusan\n- Aktif dalam kegiatan ekstrakurikuler\n- Memiliki sertifikat kompetensi\n\n## Program Studi yang Tersedia\n\n1. **Teknik Informatika**\n   - Software Engineering\n   - Artificial Intelligence\n   - Cybersecurity\n\n2. **Sistem Informasi**\n   - Business Intelligence\n   - Enterprise System\n   - IT Project Management\n\n3. **Desain Komunikasi Visual**\n   - UI/UX Design\n   - Motion Graphics\n   - Digital Marketing\n\n## Kurikulum Terintegrasi\n\n### Semester 1-6 (SMK)\n- Foundation programming\n- Web development\n- Mobile development\n- Database management\n- Project-based learning\n\n### Semester 7-12 (Universitas)\n- Advanced algorithms\n- Software architecture\n- Machine learning\n- Cloud computing\n- Capstone project\n\n## Fasilitas Universitas\n\n- Modern campus dengan lab lengkap\n- Library dengan koleksi buku IT terlengkap\n- Coworking space 24/7\n- Entrepreneurship center\n- Career development center\n- Partnership dengan 200+ perusahaan\n\n## Prospek Lulusan\n\n### Career Path\n- Software Engineer: Rp 8-15 juta (fresh graduate)\n- Mobile Developer: Rp 7-12 juta\n- DevOps Engineer: Rp 10-18 juta\n- Data Engineer: Rp 9-16 juta\n- UI/UX Designer: Rp 6-10 juta\n\n### Industry Partners\n- Startup unicorn dan decacorn\n- Multinational IT companies\n- Bank dan fintech\n- E-commerce platform\n- Software house\n\n## Timeline Pendaftaran\n\n- **Februari 2026**: Sosialisasi program\n- **Maret 2026**: Pendaftaran gelombang 1\n- **April 2026**: Tes masuk dan wawancara\n- **Mei 2026**: Pengumuman hasil\n- **Juli 2026**: Mulai perkuliahan\n\n## Cara Daftar\n\n1. Isi formulir online di website universitas\n2. Upload dokumen (rapor, portfolio, sertifikat)\n3. Bayar biaya pendaftaran Rp 200.000\n4. Ikuti tes tertulis dan wawancara\n5. Tunggu pengumuman hasil\n\nKesempatan emas ini jangan sampai terlewat! Persiapkan diri dari sekarang untuk meraih beasiswa dan menjadi sarjana IT profesional.",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-dual-degree/1200/800', 'news-dual-degree.jpg'),
                'author_id' => $guru->id,
                'published_at' => now()->subDays(30),
                'status' => 'published',
                'is_highlight' => false,
                'views' => 267,
            ],
            [
                'title' => 'Hackathon RPL 2026: Kompetisi Coding Marathon 24 Jam',
                'excerpt' => 'Jurusan RPL akan mengadakan hackathon pertama dengan hadiah total Rp 25 juta untuk para developer muda.',
                'content_md' => "# Hackathon RPL 2026: Coding, Innovation, & Victory!\n\nJurusan RPL dengan bangga mengumumkan **Hackathon RPL 2026**, kompetisi coding marathon selama 24 jam yang akan dilaksanakan pada **15-16 Februari 2026**. Event ini terbuka untuk seluruh siswa SMK se-Indonesia.\n\n## Tema: \"Tech for Society\"\n\nPeserta diminta membuat solusi teknologi untuk menyelesaikan masalah sosial seperti:\n- Pendidikan\n- Kesehatan\n- Lingkungan\n- UMKM\n- Smart city\n\n## Hadiah Total Rp 25 Juta!\n\n### Juara 1\n- Uang tunai Rp 10.000.000\n- Piala bergilir\n- Sertifikat juara\n- Merchandise eksklusif\n- Mentoring dari industry expert\n\n### Juara 2\n- Uang tunai Rp 7.000.000\n- Piala\n- Sertifikat\n- Merchandise\n\n### Juara 3\n- Uang tunai Rp 5.000.000\n- Piala\n- Sertifikat\n- Merchandise\n\n### Special Awards\n- Best UI/UX: Rp 1.500.000\n- Most Innovative: Rp 1.500.000\n- Best Pitch: Rp 1.000.000\n\n## Timeline\n\n### Hari 1 (Sabtu, 15 Februari)\n- 08.00 - 09.00: Registration & briefing\n- 09.00: Coding start!\n- 12.00 - 13.00: Lunch break\n- 18.00 - 19.00: Dinner break\n- 00.00 - 06.00: Night session (optional rest)\n\n### Hari 2 (Minggu, 16 Februari)\n- 06.00 - 07.00: Breakfast\n- 09.00: Coding stop!\n- 09.00 - 12.00: Preparation for pitching\n- 12.00 - 13.00: Lunch\n- 13.00 - 16.00: Pitching session\n- 16.00 - 17.00: Announcement & awarding\n\n## Kriteria Penilaian\n\n1. **Innovation (30%)**\n   - Keunikan ide\n   - Kreativitas solusi\n   - Problem-solving approach\n\n2. **Technical Implementation (30%)**\n   - Code quality\n   - Architecture\n   - Best practices\n   - Technology stack\n\n3. **UI/UX Design (20%)**\n   - User interface\n   - User experience\n   - Responsiveness\n   - Accessibility\n\n4. **Business Impact (20%)**\n   - Potensi pasar\n   - Scalability\n   - Social impact\n   - Pitch presentation\n\n## Juri\n\n- **Bapak Dr. Ir. Agung Trisetyanto, M.Kom.** - CTO startup unicorn\n- **Ibu Rina Kusuma, S.Kom., M.T.** - Lead Engineer perusahaan multinasional\n- **Bapak Firman Hidayat, S.Kom., MBA** - Founder software house\n- **Ibu Putri Maharani, S.Des., M.Ds.** - Senior UI/UX Designer\n\n## Fasilitas\n\n- Lab komputer 24 jam dengan AC\n- Internet dedicated 1 Gbps\n- Meals (2x breakfast, 2x lunch, 1x dinner, snacks unlimited)\n- Coffee & energy drink station\n- Sleeping area dengan sleeping bag\n- Shower room\n- First aid & medical team\n\n## Persyaratan\n\n### Tim\n- 3-4 anggota per tim\n- Minimal 1 orang siswa SMK\n- Boleh lintas sekolah\n\n### Teknis\n- Bebas menggunakan teknologi apapun\n- Boleh gunakan library/framework\n- Tidak boleh menggunakan template/clone\n- Project harus original\n\n### Administrasi\n- Biaya pendaftaran Rp 150.000/tim\n- Upload CV anggota tim\n- Surat rekomendasi sekolah\n\n## Cara Daftar\n\n1. Kunjungi: hackathon.rpl.sch.id\n2. Buat akun dan form team\n3. Lengkapi data anggota\n4. Upload dokumen\n5. Bayar biaya pendaftaran\n6. Tunggu konfirmasi panitia\n\n## Kontak\n\n- Email: hackathon@rpl.sch.id\n- WhatsApp: 0812-3456-7890\n- Instagram: @hackathonrpl2026\n\n## Sponsor\n\nEvent ini didukung oleh:\n- PT. Teknologi Nusantara (Main Sponsor)\n- CodeAcademy Indonesia\n- Dicoding Indonesia\n- BuildWithAngga\n- JetBrains\n\n---\n\n**Pendaftaran dibuka hingga 10 Februari 2026!**\n\nSlots terbatas hanya untuk 30 tim. First come first served!\n\n**#HackathonRPL2026 #TechForSociety #CodingMarathon**",
                'thumbnail' => $this->downloadImage('https://picsum.photos/seed/news-hackathon/1200/800', 'news-hackathon.jpg'),
                'author_id' => $admin->id,
                'published_at' => now()->addDays(10),
                'status' => 'draft',
                'is_highlight' => true,
                'views' => 0,
            ],
        ];

        foreach ($berita as $item) {
            Berita::create($item);
        }

        $this->command->info('✓ Berita seeded successfully (' . count($berita) . ' records)');
    }
}
