<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table for idempotency
        GlobalSetting::truncate();

        GlobalSetting::create([
            'site_name' => 'SMK Jurusan Rekayasa Perangkat Lunak',
            'site_tagline' => 'Mencetak Developer Profesional dan Berkompeten',
            'logo_primary' => null, // Will be uploaded via admin panel
            'logo_secondary' => null,
            'favicon' => null,
            'primary_color' => 'rgb(18,180,224)',
            'secondary_color' => 'rgb(255,255,255)',
            'footer_text' => '© 2026 SMK Jurusan RPL. Mencetak generasi programmer profesional yang siap bersaing di industri teknologi.',
            'contact_email' => 'info@smkrpl.sch.id',
            'contact_phone' => '(021) 1234-5678',
            'address' => 'Jl. Pendidikan No. 123, Jakarta Selatan, DKI Jakarta 12345',
        ]);

        $this->command->info('✓ Global settings seeded successfully (1 record)');
    }
}
