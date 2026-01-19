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
                <span style="color: {{ $globalSettings->primary_color_style }}">Struktur Organisasi</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                Struktur Organisasi <span style="color: {{ $globalSettings->primary_color_style }}">Konsentrasi Keahlian RPL</span>
            </h1>
            <p class="text-gray-600 text-lg">Struktur organisasi yang solid dengan tenaga pendidik dan kependidikan yang berkompeten untuk menghasilkan lulusan terbaik di bidang Rekayasa Perangkat Lunak</p>
        </div>
    </section>

    {{-- Team Members Grid --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($members->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($members as $member)
                        <a href="/struktur-organisasi/{{ $member->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group block">
                            @if($member->foto_url)
                                <img src="{{ $member->foto_url }}" alt="{{ $member->nama }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video flex items-center justify-center text-white text-4xl font-bold" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $member->nama }}</h3>
                                <p class="text-sm font-medium mb-3" style="color: {{ $globalSettings->primary_color_style }}">{{ $member->jabatan }}</p>
                                
                                @if($member->deskripsi_md)
                                    <div class="text-sm text-gray-600 line-clamp-3">
                                        {!! $member->getDeskripsiHtmlAttribute() !!}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-600">Struktur organisasi belum tersedia saat ini</p>
                </div>
            @endif
        </div>
    </section>
</div>
