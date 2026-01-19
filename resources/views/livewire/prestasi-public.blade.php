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
                <span style="color: {{ $globalSettings->primary_color_style }}">Prestasi Siswa</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                Prestasi <span style="color: {{ $globalSettings->primary_color_style }}">Siswa RPL</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-4xl mx-auto">Pencapaian dan prestasi terbaik siswa Rekayasa Perangkat Lunak</p>
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
                        placeholder="Cari nama atau posisi..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent text-gray-700"
                        style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Prestasi Grid --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($prestasis->count() > 0)
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($prestasis as $prestasi)
                        <a href="/prestasi/{{ $prestasi->id }}" class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-shadow group">
                            @if($prestasi->gambar)
                                <img src="{{ asset('storage/' . $prestasi->gambar) }}" alt="{{ $prestasi->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br" style="background-image: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }}dd)"></div>
                            @endif
                            <div class="p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                        🏆 {{ ucfirst($prestasi->jenis) }}
                                    </span>
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        {{ ucfirst($prestasi->tingkat) }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:opacity-80" style="color: {{ $globalSettings->primary_color_style }}">
                                    {{ $prestasi->judul }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-1">{{ $prestasi->nama_siswa }}</p>
                                <p class="text-xs text-gray-500">{{ $prestasi->tanggal_prestasi->format('d M Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $prestasis->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 text-lg">Tidak ada prestasi ditemukan</p>
                    <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian Anda</p>
                </div>
            @endif
        </div>
    </section>
</div>
