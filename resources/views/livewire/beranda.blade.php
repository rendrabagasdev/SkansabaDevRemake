<div>
    {{-- Hero Section --}}
    <section class="bg-white py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold text-gray-800 mb-3">
                Konsentrasi Keahlian <span style="color: {{ $globalSettings->primary_color_style }}">RPL</span>
            </h1>
            <p class="text-base sm:text-lg text-gray-600 font-semibold opacity-85 max-w-4xl mx-auto">
                Rekayasa Perangkat Lunak - Membangun Masa Depan Digital Indonesia
            </p>
        </div>
    </section>

    {{-- Landing Hero Slider (Boxed 16:9) --}}
<section class="bg-white py-5">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div 
            x-data="{ 
                currentSlide: 0,
                slides: {{ $sliders->count() }},
                autoplay: null,

                init() { this.startAutoplay() },
                startAutoplay() {
                    this.autoplay = setInterval(() => this.next(), 5000);
                },
                stopAutoplay() { clearInterval(this.autoplay) },
                next() { this.currentSlide = (this.currentSlide + 1) % this.slides },
                prev() { this.currentSlide = (this.currentSlide - 1 + this.slides) % this.slides },
                goTo(i) {
                    this.currentSlide = i;
                    this.stopAutoplay();
                    this.startAutoplay();
                }
            }"
            class="relative aspect-[2048/1072] overflow-hidden rounded-xl md:rounded-2xl shadow-lg bg-gray-900"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()"
        >

            {{-- Slides --}}
            @foreach($sliders as $index => $slider)
                <div
                    x-show="currentSlide === {{ $index }}"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-105"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute inset-0"
                >
                    <img 
                        src="{{ asset('storage/' . $slider->image) }}"
                        alt="{{ $slider->title }}"
                        class="w-full h-full object-cover"
                    >

                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                    {{-- Text --}}
                    <div class="absolute bottom-4 sm:bottom-8 left-4 sm:left-8 right-4 sm:right-8 text-white">
                        <h2 class="text-lg sm:text-2xl lg:text-4xl font-bold drop-shadow-lg">
                            {{ $slider->title }}
                        </h2>
                        @if($slider->subtitle)
                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm lg:text-lg max-w-2xl drop-shadow line-clamp-2">
                                {{ $slider->subtitle }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Arrows --}}
            <button
                @click="prev()"
                class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/60 text-white p-2 sm:p-3 rounded-full backdrop-blur transition text-xl sm:text-2xl"
            >
                ‹
            </button>

            <button
                @click="next()"
                class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/60 text-white p-2 sm:p-3 rounded-full backdrop-blur transition text-xl sm:text-2xl"
            >
                ›
            </button>

            {{-- Indicators --}}
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach($sliders as $index => $slider)
                    <button
                        @click="goTo({{ $index }})"
                        class="h-2 rounded-full transition-all"
                        :class="currentSlide === {{ $index }} ? 'bg-white w-8' : 'bg-white/50 w-3'"
                    ></button>
                @endforeach
            </div>

        </div>
    </div>
</section>


    {{-- Prospek Karir & Mata Pelajaran --}}
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-6 md:gap-8">
                {{-- Prospek Karir --}}
                <div class="bg-white rounded-xl p-6 md:p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Prospek Karir</h2>
                    </div>
                    
                    @if($prospekKarir->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($prospekKarir as $karir)
                                <div class="text-sm bg-blue-50 rounded-lg px-3 py-2 font-medium hover:bg-blue-100 transition" style="color: {{ $globalSettings->primary_color_style }}">
                                    {{ $karir->title }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Data prospek karir belum tersedia</p>
                    @endif
                </div>

                {{-- Mata Pelajaran Unggulan --}}
                <div class="bg-white rounded-xl p-6 md:p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mata Pelajaran Unggulan</h2>
                    </div>
                    
                    <div class="space-y-4">
                        @php
                        $mapel = [
                            ['title' => 'Pemrograman Web & Mobile', 'desc' => 'Mengembangkan aplikasi website dan mobile dengan teknologi modern'],
                            ['title' => 'Database Management', 'desc' => 'Mengelola dan mengoptimalkan sistem database untuk aplikasi enterprise'],
                            ['title' => 'UI/UX Design', 'desc' => 'Mendesain antarmuka pengguna yang menarik dan mudah digunakan'],
                            ['title' => 'Keamanan Siber', 'desc' => 'Melindungi sistem dan data dari ancaman keamanan digital'],
                            ['title' => 'Cloud Computing', 'desc' => 'Mengembangkan solusi berbasis cloud untuk skala enterprise'],
                            ['title' => 'Kecerdasan Buatan', 'desc' => 'Membangun sistem AI dan machine learning untuk aplikasi cerdas'],
                        ];
                        @endphp
                        @foreach($mapel as $item)
                            <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                                <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}"></div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Highlight Prestasi --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 md:mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Prestasi Siswa</h2>
                <a href="/prestasi" class="font-semibold flex items-center gap-2 hover:opacity-80 transition text-sm sm:text-base" style="color: {{ $globalSettings->primary_color_style }}">
                    Lihat Semua
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            @if($prestasi->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($prestasi as $item)
                        <a href="/prestasi/{{ $item->id }}" class="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition block">
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br" style="background-image: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }}dd)"></div>
                            @endif
                            <div class="p-4 md:p-6">
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full mb-3">
                                    🏆 {{ $item->jenis }}
                                </span>
                                <h3 class="font-bold text-base md:text-lg text-gray-900 mb-2 group-hover:text-gray-700 transition line-clamp-2">{{ $item->judul }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-1">{{ $item->penyelenggara }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $item->tanggal_prestasi->format('d M Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">Belum ada data prestasi</p>
            @endif
        </div>
    </section>

    {{-- Highlight Berita --}}
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 md:mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Berita Terbaru</h2>
                <a href="/berita" class="font-semibold flex items-center gap-2 hover:opacity-80 transition text-sm sm:text-base" style="color: {{ $globalSettings->primary_color_style }}">
                    Baca Berita Lainnya
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            @if($berita->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($berita as $item)
                        <a href="/berita/{{ $item->slug }}" class="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition block">
                            @if($item->thumbnail)
                                <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->title }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                            @endif
                            <div class="p-4 md:p-6">
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full mb-3">
                                    {{ $item->status }}
                                </span>
                                <h3 class="font-bold text-base md:text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-gray-700 transition">{{ $item->title }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $item->excerpt ?? Str::limit(strip_tags($item->content_md), 100) }}</p>
                                <p class="text-xs text-gray-500">{{ $item->published_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">Belum ada berita terbaru</p>
            @endif
        </div>
    </section>

    {{-- Mitra --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center text-gray-900 mb-8 md:mb-12">Mitra & Industri</h2>
            
            @if($mitras->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-8 items-center">
                    @foreach($mitras as $mitra)
                        @if($mitra->website)
                            <a 
                                href="{{ $mitra->website }}" 
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center justify-center p-4 rounded-lg hover:bg-gray-50 transition-all duration-300"
                                title="{{ $mitra->nama_mitra }}"
                            >
                                @if($mitra->logo)
                                    <img 
                                        src="{{ asset('storage/' . $mitra->logo) }}" 
                                        alt="{{ $mitra->nama_mitra }}"
                                        class="w-20 h-20 md:w-24 md:h-24 object-contain grayscale group-hover:grayscale-0 transition-all duration-300 group-hover:scale-110"
                                    >
                                @else
                                    <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs group-hover:bg-gray-300 transition">
                                        {{ $mitra->nama_mitra }}
                                    </div>
                                @endif
                            </a>
                        @else
                            <div class="flex items-center justify-center p-4" title="{{ $mitra->nama_mitra }}">
                                @if($mitra->logo)
                                    <img 
                                        src="{{ asset('storage/' . $mitra->logo) }}" 
                                        alt="{{ $mitra->nama_mitra }}"
                                        class="w-20 h-20 md:w-24 md:h-24 object-contain grayscale hover:grayscale-0 transition-all duration-300"
                                    >
                                @else
                                    <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                        {{ $mitra->nama_mitra }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-gray-500">Belum ada mitra yang ditampilkan</p>
                </div>
            @endif
        </div>
    </section>
</div>
