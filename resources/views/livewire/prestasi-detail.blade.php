<div class="min-h-screen bg-gray-50">
    {{-- Hero Section --}}
    <section class="bg-white py-12 border-b">
        <div class="max-w-4xl mx-auto px-6">
            <a href="/prestasi" class="inline-flex items-center gap-2 text-gray-600 hover:opacity-80 mb-6 transition" style="color: {{ $globalSettings->primary_color_style }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Prestasi
            </a>

            <div class="flex items-center gap-3 mb-4">
                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                    🏆 {{ ucfirst($prestasi->jenis) }}
                </span>
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                    {{ ucfirst($prestasi->tingkat) }}
                </span>
                <span class="text-sm text-gray-500">{{ $prestasi->tanggal_prestasi->format('d F Y') }}</span>
            </div>

            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $prestasi->judul }}</h1>

            <div class="flex items-center gap-6 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>{{ $prestasi->nama_siswa }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>{{ $prestasi->kelas }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>{{ $prestasi->penyelenggara }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                {{-- Featured Image --}}
                @if($prestasi->gambar)
                    <img src="{{ asset('storage/' . $prestasi->gambar) }}" alt="{{ $prestasi->judul }}" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br" style="background-image: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }}dd)"></div>
                @endif

                {{-- Description --}}
                <div class="p-8">
                    <div class="prose max-w-none">
                        {!! $prestasi->deskripsi_html !!}
                    </div>

                    {{-- Certificate --}}
                    @if($prestasi->sertifikat)
                        <div class="mt-8 pt-8 border-t">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Sertifikat</h3>
                            <a href="{{ asset('storage/' . $prestasi->sertifikat) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white hover:opacity-90 transition" style="background-color: {{ $globalSettings->primary_color_style }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Lihat Sertifikat
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Related Prestasi --}}
    @if($relatedPrestasis->count() > 0)
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Prestasi Terkait</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($relatedPrestasis as $related)
                        <a href="/prestasi/{{ $related->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group">
                            @if($related->gambar)
                                <img src="{{ asset('storage/' . $related->gambar) }}" alt="{{ $related->judul }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full aspect-video bg-gradient-to-br" style="background-image: linear-gradient(to bottom right, {{ $globalSettings->primary_color_style }}, {{ $globalSettings->primary_color_style }}dd)"></div>
                            @endif
                            <div class="p-4">
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full mb-3">
                                    🏆 {{ ucfirst($related->jenis) }}
                                </span>
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $related->judul }}</h3>
                                <p class="text-sm text-gray-600">{{ $related->nama_siswa }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $related->tanggal_prestasi->format('d M Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
