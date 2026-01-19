<?php

namespace Database\Seeders;

use App\Models\Mitra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MitraSeeder extends Seeder
{
    private function downloadImage($url, $filename)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200 && $imageData) {
            $path = 'mitra/' . $filename;
            Storage::disk('public')->put($path, $imageData);
            return $path;
        }
        
        return null;
    }

    public function run(): void
    {
        $mitras = [
            [
                'nama_mitra' => 'Universitas Gadjah Mada',
                'url' => 'https://picsum.photos/seed/ugm-logo/300/300',
                'filename' => 'ugm-logo.jpg',
                'website' => 'https://www.ugm.ac.id',
                'order' => 1,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'Institut Teknologi Bandung',
                'url' => 'https://picsum.photos/seed/itb-logo/300/300',
                'filename' => 'itb-logo.jpg',
                'website' => 'https://www.itb.ac.id',
                'order' => 2,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'PT Telkom Indonesia',
                'url' => 'https://picsum.photos/seed/telkom-logo/300/300',
                'filename' => 'telkom-logo.jpg',
                'website' => 'https://www.telkom.co.id',
                'order' => 3,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'PT Astra International',
                'url' => 'https://picsum.photos/seed/astra-logo/300/300',
                'filename' => 'astra-logo.jpg',
                'website' => 'https://www.astra.co.id',
                'order' => 4,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'Google Indonesia',
                'url' => 'https://picsum.photos/seed/google-logo/300/300',
                'filename' => 'google-logo.jpg',
                'website' => 'https://www.google.co.id',
                'order' => 5,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'Microsoft Indonesia',
                'url' => 'https://picsum.photos/seed/microsoft-logo/300/300',
                'filename' => 'microsoft-logo.jpg',
                'website' => 'https://www.microsoft.com/id-id',
                'order' => 6,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'Universitas Indonesia',
                'url' => 'https://picsum.photos/seed/ui-logo/300/300',
                'filename' => 'ui-logo.jpg',
                'website' => 'https://www.ui.ac.id',
                'order' => 7,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'PT Bank Central Asia',
                'url' => 'https://picsum.photos/seed/bca-logo/300/300',
                'filename' => 'bca-logo.jpg',
                'website' => 'https://www.bca.co.id',
                'order' => 8,
                'status' => 'published'
            ],
            [
                'nama_mitra' => 'Tokopedia',
                'url' => 'https://picsum.photos/seed/tokopedia-logo/300/300',
                'filename' => 'tokopedia-logo.jpg',
                'website' => null,
                'order' => 9,
                'status' => 'draft'
            ],
        ];

        echo "\n🏢 Seeding Mitra...\n";

        foreach ($mitras as $data) {
            echo "📥 Downloading {$data['nama_mitra']} logo... ";
            $logoPath = $this->downloadImage($data['url'], $data['filename']);
            
            if ($logoPath) {
                Mitra::create([
                    'nama_mitra' => $data['nama_mitra'],
                    'logo' => $logoPath,
                    'website' => $data['website'],
                    'order' => $data['order'],
                    'status' => $data['status']
                ]);
                echo "✅\n";
            } else {
                echo "❌ Failed\n";
            }
        }

        echo "\n✅ Seeded " . Mitra::count() . " mitra successfully!\n";
    }
}
