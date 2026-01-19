<?php

use App\Livewire\Auth\Login;
use App\Livewire\Beranda;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/

Route::get('/', Beranda::class)->name('beranda');

// Public pages
Route::get('/prestasi', \App\Livewire\PrestasiPublic::class)->name('prestasi.public');
Route::get('/prestasi/{id}', \App\Livewire\PrestasiDetail::class)->name('prestasi.detail');
Route::get('/berita', \App\Livewire\BeritaPublic::class)->name('berita.public');
Route::get('/berita/{slug}', \App\Livewire\BeritaDetail::class)->name('berita.detail');
Route::get('/struktur-organisasi', \App\Livewire\StrukturOrganisasiPublic::class)->name('struktur-organisasi.public');
Route::get('/struktur-organisasi/{id}', \App\Livewire\StrukturOrganisasiDetail::class)->name('struktur-organisasi.detail');
Route::get('/fasilitas', \App\Livewire\FasilitasPublic::class)->name('fasilitas.public');
Route::get('/fasilitas/{id}', \App\Livewire\FasilitasDetail::class)->name('fasilitas.detail');
Route::get('/karya', \App\Livewire\KaryaSiswaPublic::class)->name('karya.public');
Route::get('/karya/{id}', \App\Livewire\KaryaSiswaDetail::class)->name('karya.detail');
Route::get('/alumni', \App\Livewire\AlumniPublic::class)->name('alumni.public');
Route::get('/alumni/{id}', \App\Livewire\AlumniDetail::class)->name('alumni.detail');
Route::get('/unit-produksi', \App\Livewire\UnitProduksi::class)->name('unit-produksi');
Route::get('/dokumen', \App\Livewire\DokumenPublic::class)->name('dokumen.public');
Route::get('/gallery', \App\Livewire\GalleryPublic::class)->name('gallery.public');
Route::get('/gallery/{id}', \App\Livewire\GaleriDetail::class)->name('gallery.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Dashboard - accessible by admin and guru
    Route::get('/dashboard', Dashboard::class)
        ->middleware('role:admin,guru')
        ->name('dashboard');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/landing-page-slider', \App\Livewire\LandingPageSliderManager::class)->name('landing-page-slider.index');
        Route::get('/admin/fasilitas', \App\Livewire\FasilitasManager::class)->name('fasilitas.index');
        Route::get('/admin/dokumen-kurikulum', \App\Livewire\DokumenKurikulumManager::class)->name('dokumen-kurikulum.index');
        Route::get('/admin/alumni', \App\Livewire\AlumniManager::class)->name('alumni.index');
        Route::get('/admin/prospek-karir', \App\Livewire\ProspekKarirManager::class)->name('prospek-karir.index');
        Route::get('/admin/mitra', \App\Livewire\MitraManager::class)->name('mitra.index');
        Route::get('/admin/users', \App\Livewire\UserManager::class)->name('users.index');
        Route::get('/admin/global-settings', \App\Livewire\GlobalSettingsManager::class)->name('global-settings.index');
    });

    // Admin and Guru routes
    Route::middleware('role:admin,guru')->group(function () {
        Route::get('/admin/struktur-organisasi-rpl', \App\Livewire\StrukturOrganisasiRplManager::class)->name('struktur-organisasi-rpl.index');
        Route::get('/admin/prestasi', \App\Livewire\PrestasiManager::class)->name('prestasi.index');
        Route::get('/admin/karya-siswa', \App\Livewire\KaryaSiswaManager::class)->name('karya-siswa.index');
        Route::get('/admin/galeri', \App\Livewire\GaleriManager::class)->name('galeri.index');
    });

    // Logout route
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
