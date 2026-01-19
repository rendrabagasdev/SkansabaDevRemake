<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Create Users (Admin & Guru)
        $this->command->info('👤 Creating users...');
        
        User::truncate();
        
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@rpl.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $guru = User::create([
            'name' => 'Guru RPL',
            'email' => 'guru@rpl.sch.id',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✓ Users created (2 records)');
        $this->command->info('  ├─ Admin: admin@rpl.sch.id / admin123');
        $this->command->info('  └─ Guru: guru@rpl.sch.id / guru123');
        $this->command->newLine();

        // 2. Seed all content tables
        $this->command->info('📚 Seeding content tables...');
        
        $this->call([
            GlobalSettingSeeder::class,
            LandingPageSliderSeeder::class,
            StrukturOrganisasiRplSeeder::class,
            ProspekKarirSeeder::class,
            KaryaSiswaSeeder::class,
            PrestasiSeeder::class,
            BeritaSeeder::class,
            GaleriSeeder::class,
            AlumniSeeder::class,
            MitraSeeder::class,
        ]);

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📊 Summary:');
        $this->command->info('  ├─ Users: 2 records');
        $this->command->info('  ├─ Global Settings: 1 record');
        $this->command->info('  ├─ Landing Page Sliders: 3 records');
        $this->command->info('  ├─ Struktur Organisasi RPL: 7 records');
        $this->command->info('  ├─ Prospek Karir: 6 records');
        $this->command->info('  ├─ Karya Siswa: 6 records');
        $this->command->info('  ├─ Prestasi: 6 records');
        $this->command->info('  ├─ Berita: 6 records');
        $this->command->info('  ├─ Galeri: 6 records');
        $this->command->info('  ├─ Alumni: 6 records');
        $this->command->info('  └─ Mitra: 9 records');
        $this->command->newLine();
    }
}