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
                <span style="color: {{ $globalSettings->primary_color_style }}">Alumni</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                Jejak <span style="color: {{ $globalSettings->primary_color_style }}">Alumni RPL</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-4xl mx-auto">Alumni Jurusan Rekayasa Perangkat Lunak yang telah berkarir di berbagai bidang</p>
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
                        placeholder="Cari nama alumni..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent text-gray-700"
                        style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Alumni by Year --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            @if($alumniData->count() > 0)
                @foreach($alumniData as $tahun => $alumnis)
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full text-white font-bold" style="background-color: {{ $globalSettings->primary_color_style }}">
                                {{ substr($tahun, -2) }}
                            </span>
                            Angkatan {{ $tahun }}
                        </h2>
                        
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($alumnis as $alumni)
                                <a href="/alumni/{{ $alumni->id }}" class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-shadow group block">
                                    @if($alumni->foto_url)
                                        <img src="{{ $alumni->foto_url }}" alt="{{ $alumni->nama }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full aspect-video flex items-center justify-center text-white text-4xl font-bold" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                            {{ strtoupper(substr($alumni->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $alumni->nama }}</h3>
                                        
                                        <div class="space-y-2 mb-3">
                                            @if($alumni->status_alumni)
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="inline-flex px-2 py-1 rounded text-xs font-semibold" style="background-color: {{ $globalSettings->primary_color_style }}22; color: {{ $globalSettings->primary_color_style }}">
                                                        {{ $alumni->status_alumni }}
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            @if($alumni->institusi)
                                                <p class="text-sm text-gray-600">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $alumni->institusi }}
                                                </p>
                                            @endif
                                            
                                            @if($alumni->bidang)
                                                <p class="text-sm text-gray-600">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $alumni->bidang }}
                                                </p>
                                            @endif
                                        </div>

                                        @if($alumni->deskripsi)
                                            <div class="text-sm text-gray-600 line-clamp-3 prose prose-sm max-w-none">
                                                {!! $alumni->deskripsi_html !!}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: {{ $globalSettings->primary_color_style }}22">
                        <svg class="w-8 h-8" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data Alumni</h3>
                    <p class="text-gray-600">
                        @if($search || $filterStatus)
                            Tidak ada alumni yang sesuai dengan pencarian Anda
                        @else
                            Data alumni belum tersedia saat ini
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
