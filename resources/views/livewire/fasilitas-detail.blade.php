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
                <a href="/fasilitas" class="hover:opacity-80">Fasilitas</a>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">{{ $fasilitas->tempat }}</span>
            </div>
            
            {{-- Back Button --}}
            <a href="/fasilitas" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Fasilitas
            </a>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                {{-- Featured Image --}}
                @if($fasilitas->gambar_url)
                    <img src="{{ $fasilitas->gambar_url }}" alt="{{ $fasilitas->tempat }}" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                @endif

                <div class="p-8">
                    {{-- Title --}}
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">{{ $fasilitas->tempat }}</h1>

                    {{-- Description --}}
                    @if($fasilitas->deskripsi)
                        <div class="prose prose-lg max-w-none mb-8">
                            {!! $fasilitas->deskripsi_html !!}
                        </div>
                    @endif

                    {{-- Fasilitas List --}}
                    @if($fasilitas->fasilitas && count($fasilitas->fasilitas) > 0)
                        <div class="bg-gray-50 rounded-lg p-6 border-2" style="border-color: {{ $globalSettings->primary_color_style }}22">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Fasilitas yang Tersedia</h3>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($fasilitas->fasilitas as $facility)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 shrink-0" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $facility }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Fasilitas --}}
            @if($relatedFasilitas->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Fasilitas Lainnya</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedFasilitas as $related)
                            <a href="/fasilitas/{{ $related->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group">
                                @if($related->gambar_url)
                                    <img src="{{ $related->gambar_url }}" alt="{{ $related->tempat }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 line-clamp-2">{{ $related->tempat }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
