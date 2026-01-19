<div class="min-h-screen bg-gray-50">
    {{-- Header dengan Breadcrumb --}}
    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-6 text-center">
            {{-- Breadcrumb --}}
            <div class="flex items-center justify-center gap-2 text-sm text-gray-600 mb-4">
                <svg class="w-5 h-5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="hover:opacity-80 cursor-pointer">Beranda</span>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">Karya Siswa</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                Karya <span style="color: {{ $globalSettings->primary_color_style }}">Siswa</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-4xl mx-auto">Portofolio dan proyek karya siswa RPL</p>
        </div>
    </section>

    {{-- Search Only --}}
    <section class="py-6 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="max-w-2xl">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari karya atau nama siswa..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent text-gray-700"
                        style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Karya Grid --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($karyas->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($karyas as $karya)
                        <a href="/karya/{{ $karya->id }}" class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-shadow group block">
                            @if($karya->gambar_url)
                                <img src="{{ $karya->gambar_url }}" alt="{{ $karya->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    @if($karya->kategori)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-cyan-500/10 text-cyan-600">
                                            {{ $karya->kategori }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                        {{ $karya->tahun }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-gray-700 transition">{{ $karya->judul }}</h3>
                                
                                @if($karya->nama_siswa)
                                    <p class="text-sm text-gray-600 mb-2">
                                        <span class="font-medium">Oleh:</span> {{ $karya->nama_siswa }}
                                        @if($karya->kelas)
                                            <span class="text-gray-400">• {{ $karya->kelas }}</span>
                                        @endif
                                    </p>
                                @endif

                                @if($karya->deskripsi)
                                    <div class="text-sm text-gray-600 line-clamp-3 prose prose-sm max-w-none">
                                        {!! $karya->deskripsi_html !!}
                                    </div>
                                @endif

                                @if($karya->teknologi && is_array($karya->teknologi))
                                    <div class="flex flex-wrap gap-1 mt-3">
                                        @foreach(array_slice($karya->teknologi, 0, 3) as $tech)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $tech }}
                                            </span>
                                        @endforeach
                                        @if(count($karya->teknologi) > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                +{{ count($karya->teknologi) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: {{ $globalSettings->primary_color_style }}22">
                        <svg class="w-8 h-8" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Karya</h3>
                    <p class="text-gray-600">
                        @if($search || $filterKategori)
                            Tidak ada karya yang sesuai dengan pencarian Anda
                        @else
                            Karya siswa belum tersedia saat ini
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
