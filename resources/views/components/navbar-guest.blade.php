<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            {{-- Logo & Brand --}}
            <div class="flex items-center gap-3 lg:flex">
                @if($globalSettings->logo_primary_url)
                    <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }})">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif
                <div class="hidden lg:block">
                    <div class="font-bold text-lg leading-tight" style="color: {{ $globalSettings->primary_color_style }}">{{ $globalSettings->site_name ?? 'Rekayasa Perangkat Lunak' }}</div>
                    <div class="text-xs text-gray-600">{{ $globalSettings->site_tagline ?? 'SMK Negeri 1 Bantul' }}</div>
                </div>
            </div>

            {{-- Desktop Menu --}}
            <div class="hidden lg:flex items-center gap-6">
                <a href="/" class="text-gray-900 font-medium transition hover-primary">Beranda</a>
                
                {{-- Profil Dropdown --}}
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1 text-gray-600 transition hover-primary">
                        Profil
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="absolute top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2">
                        <a href="/struktur-organisasi" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Struktur Organisasi</a>
                        <a href="/fasilitas" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Fasilitas</a>
                    </div>
                </div>

                {{-- Siswa Dropdown --}}
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1 text-gray-600 transition hover-primary">
                        Siswa
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="absolute top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2">
                        <a href="/prestasi" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Prestasi Siswa</a>
                        <a href="/karya" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Karya Siswa</a>
                        <a href="/alumni" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Alumni</a>
                    </div>
                </div>

                <a href="/unit-produksi" class="text-gray-600 transition hover-primary">Unit Produksi</a>

<style>
    .hover-primary:hover {
        color: {{ $globalSettings->primary_color_style }} !important;
    }
</style>

                {{-- Informasi Dropdown --}}
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1 text-gray-600 transition hover-primary">
                        Informasi
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="absolute top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2">
                        <a href="/dokumen" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Dokumen</a>
                        <a href="/gallery" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 hover-primary">Gallery</a>
                    </div>
                </div>
            </div>

            {{-- Login Button --}}
            <div class="hidden lg:flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <a href="/login" class="font-medium transition hover-primary">Masuk</a>
            </div>

            {{-- Mobile Menu Button --}}
            <button 
                x-data
                @click="$dispatch('toggle-mobile-menu')"
                class="lg:hidden p-2 text-gray-600 hover:text-[#12B4E0]"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div 
        x-data="{ open: false }"
        @toggle-mobile-menu.window="open = !open"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="lg:hidden fixed top-0 left-0 w-full bg-white border-b border-gray-200 shadow-lg z-50"
        style="min-height: 100vh;"
    >
        <div class="flex items-center justify-between px-6 pt-6 pb-2">
            <div class="flex items-center gap-3">
                @if($globalSettings->logo_primary_url)
                    <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }})">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="font-bold text-lg leading-tight" style="color: {{ $globalSettings->primary_color_style }}">{{ $globalSettings->site_name ?? 'Rekayasa Perangkat Lunak' }}</div>
                    <div class="text-xs text-gray-600">{{ $globalSettings->site_tagline ?? 'SMK Negeri 1 Bantul' }}</div>
                </div>
            </div>
            <button @click="open = false" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 focus:outline-none" aria-label="Tutup menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-2 space-y-2">
            <a href="/" class="block py-2 text-gray-900 font-medium hover-primary">Beranda</a>
            
            {{-- Profil Mobile Accordion --}}
            <div x-data="{ expanded: false }" class="border-b border-gray-100 pb-2 mb-2">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between py-2 text-gray-600 hover-primary font-semibold">
                    <span>Profil</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="expanded" x-transition class="pl-4 space-y-1">
                    <a href="/struktur-organisasi" class="block py-2 text-sm text-gray-600 hover-primary">Struktur Organisasi</a>
                    <a href="/fasilitas" class="block py-2 text-sm text-gray-600 hover-primary">Fasilitas</a>
                </div>
            </div>

            {{-- Siswa Mobile Accordion --}}
            <div x-data="{ expanded: false }" class="border-b border-gray-100 pb-2 mb-2">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between py-2 text-gray-600 hover-primary font-semibold">
                    <span>Siswa</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="expanded" x-transition class="pl-4 space-y-1">
                    <a href="/prestasi" class="block py-2 text-sm text-gray-600 hover-primary">Prestasi Siswa</a>
                    <a href="/karya" class="block py-2 text-sm text-gray-600 hover-primary">Karya Siswa</a>
                    <a href="/alumni" class="block py-2 text-sm text-gray-600 hover-primary">Alumni</a>
                </div>
            </div>

            <a href="/unit-produksi" class="block py-2 text-gray-600 hover-primary">Unit Produksi</a>

            {{-- Informasi Mobile Accordion --}}
            <div x-data="{ expanded: false }" class="border-b border-gray-100 pb-2 mb-2">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between py-2 text-gray-600 hover-primary font-semibold">
                    <span>Informasi</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="expanded" x-transition class="pl-4 space-y-1">
                    <a href="/dokumen" class="block py-2 text-sm text-gray-600 hover-primary">Dokumen</a>
                    <a href="/gallery" class="block py-2 text-sm text-gray-600 hover-primary">Gallery</a>
                </div>
            </div>

            <a href="/login" class="block py-2 font-medium pt-2 border-t hover-primary">Masuk</a>
        </div>
    </div>
</nav>
