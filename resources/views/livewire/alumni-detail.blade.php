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
                <a href="/alumni" class="hover:opacity-80">Alumni</a>
                <span>›</span>
                <span style="color: {{ $globalSettings->primary_color_style }}">{{ $alumni->nama }}</span>
            </div>
            
            {{-- Back Button --}}
            <a href="/alumni" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Alumni
            </a>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row gap-8">
                        {{-- Photo --}}
                        <div class="md:w-1/3">
                            @if($alumni->foto_url)
                                <img src="{{ $alumni->foto_url }}" alt="{{ $alumni->nama }}" class="w-full aspect-square object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center text-white text-6xl font-bold rounded-lg shadow-md" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                    {{ strtoupper(substr($alumni->nama, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="md:w-2/3">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $alumni->nama }}</h1>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <span class="text-sm text-gray-500">Tahun Lulus</span>
                                        <p class="font-semibold text-gray-900">{{ $alumni->tahun_lulus }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                                    </svg>
                                    <div>
                                        <span class="text-sm text-gray-500">Status</span>
                                        <p class="font-semibold text-gray-900">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $alumni->badge_color }}22; color: {{ $alumni->badge_color }}">
                                                {{ $alumni->status_alumni }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                @if($alumni->institusi)
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                        </svg>
                                        <div>
                                            <span class="text-sm text-gray-500">Institusi</span>
                                            <p class="font-semibold text-gray-900">{{ $alumni->institusi }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($alumni->bidang)
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                                        </svg>
                                        <div>
                                            <span class="text-sm text-gray-500">Bidang</span>
                                            <p class="font-semibold text-gray-900">{{ $alumni->bidang }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Description --}}
                            @if($alumni->deskripsi)
                                <div class="pt-6 border-t">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3">Tentang</h3>
                                    <div class="prose prose-lg max-w-none text-gray-600">
                                        {!! $alumni->deskripsi_html !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Alumni --}}
            @if($relatedAlumni->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Alumni Lainnya</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedAlumni as $related)
                            <a href="/alumni/{{ $related->id }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition group">
                                @if($related->foto_url)
                                    <img src="{{ $related->foto_url }}" alt="{{ $related->nama }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full aspect-video flex items-center justify-center text-white text-4xl font-bold" style="background: linear-gradient(135deg, {{ $globalSettings->primary_color_style }} 0%, {{ $globalSettings->primary_color_style }}cc 100%)">
                                        {{ strtoupper(substr($related->nama, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $related->nama }}</h3>
                                    <p class="text-sm text-gray-600">{{ $related->tahun_lulus }} • {{ $related->status_alumni }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
