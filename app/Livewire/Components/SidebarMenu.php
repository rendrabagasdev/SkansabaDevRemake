<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class SidebarMenu extends Component
{
    public bool $isMobileOpen = false;

    #[On('toggle-mobile-sidebar')]
    public function toggleMobileSidebar()
    {
        $this->isMobileOpen = !$this->isMobileOpen;
    }

    /**
     * Get menu items with role-based filtering
     */
    public function getMenuItems(): array
    {
        $user = auth()->user();
        $allMenuItems = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
                'access' => ['admin', 'guru'],
            ],
            [
                'key' => 'landing_page_slider',
                'label' => 'Landing Page Slider',
                'route' => 'landing-page-slider.index',
                'icon' => 'image',
                'access' => ['admin'],
            ],
            [
                'key' => 'struktur_organisasi_rpl',
                'label' => 'Struktur Organisasi RPL',
                'route' => 'struktur-organisasi-rpl.index',
                'icon' => 'users',
                'access' => ['admin', 'guru'], // Changed to admin+guru access
            ],
            [
                'key' => 'prestasi_siswa',
                'label' => 'Prestasi Siswa',
                'route' => 'prestasi.index',
                'icon' => 'star',
                'access' => ['admin', 'guru'],
            ],
            [
                'key' => 'karya_siswa',
                'label' => 'Karya Siswa',
                'route' => 'karya-siswa.index',
                'icon' => 'code',
                'access' => ['admin', 'guru'],
            ],
            [
                'key' => 'fasilitas',
                'label' => 'Fasilitas',
                'route' => 'fasilitas.index',
                'icon' => 'building',
                'access' => ['admin'],
            ],
            [
                'key' => 'unit_produksi',
                'label' => 'Unit Produksi',
                'route' => null,
                'external' => true,
                'url' => '#', // Configure in settings
                'icon' => 'shopping-bag',
                'access' => ['admin', 'guru'],
            ],
            [
                'key' => 'dokumen',
                'label' => 'Dokumen & Kurikulum',
                'route' => 'dokumen-kurikulum.index',
                'icon' => 'folder',
                'access' => ['admin'],
            ],
            [
                'key' => 'galeri',
                'label' => 'Galeri',
                'route' => 'galeri.index',
                'icon' => 'photograph',
                'access' => ['admin', 'guru'],
            ],
            [
                'key' => 'alumni',
                'label' => 'Alumni',
                'route' => 'alumni.index',
                'icon' => 'academic-cap',
                'access' => ['admin'],
            ],
            [
                'key' => 'prospek_karir',
                'label' => 'Prospek Karir',
                'route' => 'prospek-karir.index',
                'icon' => 'briefcase',
                'access' => ['admin'],
            ],
            [
                'key' => 'mitra',
                'label' => 'Mitra & Industri',
                'route' => 'mitra.index',
                'icon' => 'office-building',
                'access' => ['admin'],
            ],
            [
                'key' => 'users',
                'label' => 'Manajemen Pengguna',
                'route' => 'users.index',
                'icon' => 'users',
                'access' => ['admin'],
            ],
            [
                'key' => 'global_settings',
                'label' => 'Pengaturan Global',
                'route' => 'global-settings.index',
                'icon' => 'cog',
                'access' => ['admin'],
            ],
        ];

        // Filter menu items based on user role
        return collect($allMenuItems)
            ->filter(fn($item) => in_array($user->role, $item['access']))
            ->values()
            ->toArray();
    }

    /**
     * Check if menu item is active
     */
    public function isActive(string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        return request()->routeIs($routeName . '*');
    }

    /**
     * Toggle mobile menu
     */
    public function toggleMobile()
    {
        $this->isMobileOpen = !$this->isMobileOpen;
    }

    /**
     * Close mobile menu
     */
    public function closeMobile()
    {
        $this->isMobileOpen = false;
    }

    public function render()
    {
        return view('livewire.components.sidebar-menu', [
            'menuItems' => $this->getMenuItems(),
        ]);
    }
}
