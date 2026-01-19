<div class="min-h-screen bg-gray-50">
    {{-- Header dengan Breadcrumb --}}
    <section class="bg-white py-8 border-b">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <svg class="w-5 h-5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <a href="/" class="hover:opacity-80">Beranda</a>
                <span>›</span>
                <a href="/gallery" class="hover:opacity-80">Galeri</a>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">{{ Str::limit($galeri->judul, 30) }}</span>
            </div>
            
            {{-- Back Button --}}
            <a href="/gallery" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Galeri
            </a>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                {{-- Featured Image --}}
                @if($galeri->gambar_url)
                    <img src="{{ $galeri->gambar_url }}" alt="{{ $galeri->judul }}" class="w-full h-auto object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                <div class="p-8">
                    {{-- Kategori Badge --}}
                    @if($galeri->kategori)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-cyan-500/10 text-cyan-600">
                                {{ $galeri->kategori }}
                            </span>
                        </div>
                    @endif

                    {{-- Title --}}
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">{{ $galeri->judul }}</h1>

                    {{-- Description --}}
                    @if($galeri->deskripsi)
                        <div class="prose prose-lg max-w-none text-gray-600">
                            {!! $galeri->deskripsi_html !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Galeri --}}
            @if($relatedGaleris->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Galeri Lainnya</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedGaleris as $related)
                            <a href="/gallery/{{ $related->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group">
                                @if($related->gambar_url)
                                    <img src="{{ $related->gambar_url }}" alt="{{ $related->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 line-clamp-2">{{ $related->judul }}</h3>
                                    @if($related->kategori)
                                        <p class="text-sm text-cyan-600 mt-1">{{ $related->kategori }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
