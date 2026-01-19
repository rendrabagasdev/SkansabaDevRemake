<?php

namespace Database\Seeders;

use App\Models\ProspekKarir;
use Illuminate\Database\Seeder;

class ProspekKarirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table for idempotency
        ProspekKarir::truncate();

        $prospekKarirs = [
            [
                'title' => 'Full Stack Web Developer',
                'description' => "**Peluang Karir:**\nMenjadi developer yang menguasai frontend dan backend untuk membangun aplikasi web kompleks.\n\n**Skill yang Dibutuhkan:**\n- HTML, CSS, JavaScript\n- PHP, Laravel, atau Node.js\n- Database Management (MySQL, PostgreSQL)\n- Git Version Control\n\n**Rata-rata Gaji:** Rp 8.000.000 - Rp 15.000.000/bulan",
                'icon' => '💻',
                'order' => 0,
                'is_active' => true,
                'image' => null,
            ],
            [
                'title' => 'Mobile App Developer',
                'description' => "**Peluang Karir:**\nMengembangkan aplikasi mobile untuk platform Android dan iOS yang digunakan jutaan pengguna.\n\n**Skill yang Dibutuhkan:**\n- Kotlin/Java untuk Android\n- Swift untuk iOS\n- React Native atau Flutter\n- REST API Integration\n\n**Rata-rata Gaji:** Rp 7.000.000 - Rp 14.000.000/bulan",
                'icon' => '📱',
                'order' => 1,
                'is_active' => true,
                'image' => null,
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => "**Peluang Karir:**\nMerancang antarmuka dan pengalaman pengguna yang intuitif untuk aplikasi dan website.\n\n**Skill yang Dibutuhkan:**\n- Figma, Adobe XD, Sketch\n- User Research & Testing\n- Wireframing & Prototyping\n- Design System\n\n**Rata-rata Gaji:** Rp 6.000.000 - Rp 12.000.000/bulan",
                'icon' => '🎨',
                'order' => 2,
                'is_active' => true,
                'image' => null,
            ],
            [
                'title' => 'Software Quality Assurance',
                'description' => "**Peluang Karir:**\nMemastikan kualitas software melalui testing dan automation untuk menghasilkan produk bebas bug.\n\n**Skill yang Dibutuhkan:**\n- Manual & Automation Testing\n- Selenium, Cypress, Jest\n- Test Case Design\n- Bug Tracking Tools\n\n**Rata-rata Gaji:** Rp 6.000.000 - Rp 11.000.000/bulan",
                'icon' => '🔍',
                'order' => 3,
                'is_active' => true,
                'image' => null,
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => "**Peluang Karir:**\nMengelola infrastruktur server, deployment automation, dan memastikan sistem berjalan optimal.\n\n**Skill yang Dibutuhkan:**\n- Linux Server Administration\n- Docker & Kubernetes\n- CI/CD Pipeline (Jenkins, GitLab CI)\n- Cloud Platform (AWS, GCP, Azure)\n\n**Rata-rata Gaji:** Rp 9.000.000 - Rp 16.000.000/bulan",
                'icon' => '⚙️',
                'order' => 4,
                'is_active' => true,
                'image' => null,
            ],
            [
                'title' => 'Database Administrator',
                'description' => "**Peluang Karir:**\nMengelola, mengoptimasi, dan mengamankan database untuk mendukung aplikasi enterprise.\n\n**Skill yang Dibutuhkan:**\n- SQL & NoSQL Database\n- Database Optimization\n- Backup & Recovery\n- Security & Access Control\n\n**Rata-rata Gaji:** Rp 7.000.000 - Rp 13.000.000/bulan",
                'icon' => '🗄️',
                'order' => 5,
                'is_active' => true,
                'image' => null,
            ],
        ];

        foreach ($prospekKarirs as $prospekKarir) {
            ProspekKarir::create($prospekKarir);
        }

        $this->command->info('✓ Prospek karir seeded successfully (6 records)');
    }
}
