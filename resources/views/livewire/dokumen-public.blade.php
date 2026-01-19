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
                <span style="color: {{ $globalSettings->primary_color_style }}">Dokumen</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                Dokumen <span style="color: {{ $globalSettings->primary_color_style }}">Kurikulum</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-4xl mx-auto">Unduh dokumen kurikulum dan silabus Jurusan RPL</p>
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
                        placeholder="Cari dokumen..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent text-gray-700"
                        style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Documents Grid --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($dokumens->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($dokumens as $dokumen)
                        <div class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-shadow group">
                            {{-- Document Icon/Header --}}
                            <div class="aspect-video flex items-center justify-center relative" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                <svg class="w-20 h-20 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                
                                {{-- Badge Jenis --}}
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                        {{ $dokumen->jenis }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Document Info --}}
                            <div class="p-5">
                                <h3 class="text-base font-bold text-gray-900 mb-3 line-clamp-2 min-h-[3rem]">
                                    {{ $dokumen->judul }}
                                </h3>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $dokumen->tahun_berlaku }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>{{ $dokumen->formatted_file_size }}</span>
                                    </div>
                                </div>
                                
                                {{-- Download Button --}}
                                @if($dokumen->file_url)
                                    <a href="{{ $dokumen->file_url }}" download class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-white rounded-lg transition font-medium hover:shadow-md" style="background-color: {{ $globalSettings->primary_color_style }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Unduh Dokumen
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: {{ $globalSettings->primary_color_style }}22">
                        <svg class="w-8 h-8" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Dokumen</h3>
                    <p class="text-gray-600">
                        @if($search || $filterJenis)
                            Tidak ada dokumen yang sesuai dengan pencarian Anda
                        @else
                            Dokumen kurikulum belum tersedia saat ini
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
