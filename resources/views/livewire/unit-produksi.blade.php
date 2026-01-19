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
                <span style="color: {{ $globalSettings->primary_color_style }}">Unit Produksi</span>
            </div>
            
            <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-3">
                <span style="color: {{ $globalSettings->primary_color_style }}">Unit Produksi</span> RPL
            </h1>
            <p class="text-gray-600 text-lg">Layanan dan produk dari Jurusan RPL</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4" style="color: {{ $globalSettings->primary_color_style }}">
                        Tentang Unit Produksi
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Unit Produksi Jurusan Rekayasa Perangkat Lunak (RPL) adalah wadah bagi siswa untuk mengaplikasikan 
                        ilmu yang telah dipelajari dalam bentuk proyek nyata. Melalui unit produksi ini, siswa dapat 
                        mengembangkan aplikasi, website, dan solusi digital lainnya untuk klien internal maupun eksternal sekolah.
                    </p>

                    <h3 class="text-xl font-bold text-gray-900 mb-3 mt-8">Layanan yang Kami Tawarkan</h3>
                    <div class="grid md:grid-cols-2 gap-4 mb-8">
                        <div class="p-4 border rounded-lg hover:border-gray-300 transition" style="border-color: {{ $globalSettings->primary_color_style }}22">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}22">
                                    <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Pembuatan Website</h4>
                                    <p class="text-sm text-gray-600">Website company profile, e-commerce, dan sistem informasi</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border rounded-lg hover:border-gray-300 transition" style="border-color: {{ $globalSettings->primary_color_style }}22">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}22">
                                    <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Aplikasi Mobile</h4>
                                    <p class="text-sm text-gray-600">Aplikasi Android dan iOS untuk berbagai kebutuhan</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border rounded-lg hover:border-gray-300 transition" style="border-color: {{ $globalSettings->primary_color_style }}22">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}22">
                                    <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Sistem Informasi</h4>
                                    <p class="text-sm text-gray-600">Sistem manajemen data dan informasi berbasis web</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border rounded-lg hover:border-gray-300 transition" style="border-color: {{ $globalSettings->primary_color_style }}22">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}22">
                                    <svg class="w-6 h-6" style="color: {{ $globalSettings->primary_color_style }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Maintenance & Support</h4>
                                    <p class="text-sm text-gray-600">Layanan pemeliharaan dan dukungan teknis aplikasi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-3 mt-8">Mengapa Memilih Kami?</h3>
                    <ul class="space-y-2 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600">Dikerjakan oleh siswa RPL yang terlatih dan berpengalaman</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600">Harga terjangkau dan kompetitif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600">Menggunakan teknologi terkini dan modern</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: {{ $globalSettings->primary_color_style }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600">Mendukung pendidikan dan pengembangan siswa</span>
                        </li>
                    </ul>

                    <div class="bg-gray-50 rounded-lg p-6 mt-8 border" style="border-color: {{ $globalSettings->primary_color_style }}22">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Tertarik dengan Layanan Kami?</h3>
                        <p class="text-gray-600 mb-4">
                            Hubungi kami untuk konsultasi dan penawaran terbaik sesuai kebutuhan Anda.
                        </p>
                        <a href="/kontak" class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg font-medium hover:opacity-90 transition" style="background-color: {{ $globalSettings->primary_color_style }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
