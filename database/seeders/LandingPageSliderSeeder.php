<?php

namespace Database\Seeders;

use App\Models\LandingPageSlider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LandingPageSliderSeeder extends Seeder
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
            
            $path = 'sliders/' . $filename;
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
        LandingPageSlider::truncate();

        $sliders = [
            [
                'title' => 'Selamat Datang di Jurusan RPL',
                'subtitle' => 'Bergabunglah dengan kami untuk menjadi developer profesional yang siap menghadapi tantangan industri teknologi masa depan',
                'link' => '/tentang-kami',
                'order' => 0,
                'is_active' => true,
                'image' => $this->downloadImage('https://picsum.photos/seed/slider-1/1920/1080', 'slider-1.jpg'),
            ],
            [
                'title' => 'Kurikulum Berbasis Industri',
                'subtitle' => 'Pelajari teknologi terkini seperti Web Development, Mobile Development, dan Software Engineering dengan instruktur berpengalaman',
                'link' => '/kurikulum',
                'order' => 1,
                'is_active' => true,
                'image' => $this->downloadImage('https://picsum.photos/seed/slider-2/1920/1080', 'slider-2.jpg'),
            ],
            [
                'title' => 'Fasilitas Laboratorium Modern',
                'subtitle' => 'Praktik langsung dengan perangkat dan software industri standar untuk mempersiapkan karir di dunia teknologi',
                'link' => '/fasilitas',
                'order' => 2,
                'is_active' => true,
                'image' => $this->downloadImage('https://picsum.photos/seed/slider-3/1920/1080', 'slider-3.jpg'),
            ],
        ];

        foreach ($sliders as $slider) {
            LandingPageSlider::create($slider);
        }

        $this->command->info('✓ Landing page sliders seeded successfully (3 records)');
    }
}
