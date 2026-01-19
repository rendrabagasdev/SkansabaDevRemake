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
                <a href="/karya" class="hover:opacity-80">Karya Siswa</a>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">{{ Str::limit($karya->judul, 30) }}</span>
            </div>
            
            {{-- Back Button --}}
            <a href="/karya" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Karya Siswa
            </a>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                {{-- Featured Image --}}
                @if($karya->gambar_url)
                    <img src="{{ $karya->gambar_url }}" alt="{{ $karya->judul }}" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                @endif

                <div class="p-8">
                    {{-- Metadata --}}
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        @if($karya->kategori)
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full" style="background-color: {{ $globalSettings->primary_color_style }}22; color: {{ $globalSettings->primary_color_style }}">
                                {{ $karya->kategori }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">{{ $karya->tahun }}</span>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $karya->judul }}</h1>

                    {{-- Author Info --}}
                    @if($karya->nama_siswa)
                        <div class="flex items-center gap-2 text-gray-600 mb-6 pb-6 border-b">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ $karya->nama_siswa }}</span>
                            @if($karya->kelas)
                                <span class="text-gray-400">•</span>
                                <span>{{ $karya->kelas }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Description --}}
                    @if($karya->deskripsi)
                        <div class="prose prose-lg max-w-none mb-8">
                            {!! $karya->deskripsi_html !!}
                        </div>
                    @endif

                    {{-- Teknologi --}}
                    @if($karya->teknologi && is_array($karya->teknologi) && count($karya->teknologi) > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Teknologi yang Digunakan</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($karya->teknologi as $tech)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-3 pt-6 border-t">
                        @if($karya->url_demo)
                            <a href="{{ $karya->url_demo }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium text-white hover:opacity-90 transition" style="background-color: {{ $globalSettings->primary_color_style }}">>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Demo
                            </a>
                        @endif

                        @if($karya->url_repo)
                            <a href="{{ $karya->url_repo }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium border-2 hover:bg-gray-50 transition" style="border-color: {{ $globalSettings->primary_color_style }}; color: {{ $globalSettings->primary_color_style }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Source Code
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Karya --}}
            @if($relatedKaryas->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Karya Lainnya</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedKaryas as $related)
                            <a href="/karya/{{ $related->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group">
                                @if($related->gambar_url)
                                    <img src="{{ $related->gambar_url }}" alt="{{ $related->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300"></div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ $related->judul }}</h3>
                                    <p class="text-sm text-gray-600">{{ $related->nama_siswa }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
