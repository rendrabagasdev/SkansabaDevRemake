<div class="min-h-screen bg-gray-50">
    {{-- Header dengan Breadcrumb --}}
    <section class="bg-white py-8 border-b">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <svg class="w-5 h-5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="hover:opacity-80 cursor-pointer">Beranda</span>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">Fasilitas</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                <span style="color: {{ $globalSettings->primary_color_style }}">Fasilitas</span> & Sarana
            </h1>
            <p class="text-gray-600 text-lg">Fasilitas dan sarana pembelajaran Jurusan RPL</p>
        </div>
    </section>

    {{-- Fasilitas Grid --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($fasilitas->count() > 0)
                <div class="grid md:grid-cols-2 gap-8">
                    @foreach($fasilitas as $item)
                        <a href="/fasilitas/{{ $item->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition block">
                            @if($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->tempat }}" class="w-full aspect-video object-cover">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                            @endif
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item->tempat }}</h3>
                                
                                @if($item->deskripsi)
                                    <div class="text-gray-600 mb-4 prose prose-sm max-w-none">
                                        {!! $item->deskripsi_html !!}
                                    </div>
                                @endif

                                @if($item->fasilitas && count($item->fasilitas) > 0)
                                    <div class="border-t pt-4 mt-4">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Fasilitas yang Tersedia:</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($item->fasilitas as $facility)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $globalSettings->primary_color_style }}22; color: {{ $globalSettings->primary_color_style }}">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $facility }}
                                                </span>
                                            @endforeach
                                        </div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-600">Informasi fasilitas belum tersedia saat ini</p>
                </div>
            @endif
        </div>
    </section>
</div>
