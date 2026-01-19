<div class="min-h-screen bg-gray-50">
    {{-- Header dengan Breadcrumb --}}
    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center justify-center gap-2 text-sm text-gray-600 mb-6">
                <svg class="w-5 h-5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <a href="/" class="hover:opacity-80">Beranda</a>
                <span>›</span>
                <a href="/struktur-organisasi" class="hover:opacity-80">Struktur Organisasi</a>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">{{ $member->nama }}</span>
            </div>
            
            {{-- Back Button --}}
            <div class="flex justify-center">
                <a href="/struktur-organisasi" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Struktur Organisasi
                </a>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row gap-8">
                        {{-- Photo --}}
                        <div class="md:w-1/3">
                            @if($member->foto_url)
                                <img src="{{ $member->foto_url }}" alt="{{ $member->nama }}" class="w-full aspect-square object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center text-white text-6xl font-bold rounded-lg shadow-md" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="md:w-2/3">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $member->nama }}</h1>
                            <p class="text-xl font-medium mb-6" style="color: {{ $globalSettings->primary_color_style }}">{{ $member->jabatan }}</p>

                            {{-- Description --}}
                            @if($member->deskripsi_md)
                                <div class="prose prose-lg max-w-none text-gray-600">
                                    {!! $member->getDeskripsiHtmlAttribute() !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Members --}}
            @if($relatedMembers->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Anggota Lainnya</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedMembers as $related)
                            <a href="/struktur-organisasi/{{ $related->id }}" class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-shadow group">
                                @if($related->foto_url)
                                    <img src="{{ $related->foto_url }}" alt="{{ $related->nama }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full aspect-video flex items-center justify-center text-white text-4xl font-bold" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                        {{ strtoupper(substr($related->nama, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $related->nama }}</h3>
                                    <p class="text-sm" style="color: {{ $globalSettings->primary_color_style }}">{{ $related->jabatan }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
