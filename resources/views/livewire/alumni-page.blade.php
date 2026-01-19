<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Alumni Jurusan RPL</h1>
            <p class="text-lg text-gray-600">Bangga dengan pencapaian alumni kami di berbagai bidang</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Tahun Lulus</label>
                    <select 
                        wire:model.live="filterTahunLulus" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                    >
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 1990; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Status</label>
                    <select 
                        wire:model.live="filterStatusAlumni" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                    >
                        <option value="">Semua Status</option>
                        <option value="kuliah">Kuliah</option>
                        <option value="kerja">Bekerja</option>
                        <option value="wirausaha">Wirausaha</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Alumni Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($alumniList as $alumni)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                    {{-- Foto --}}
                    <div class="aspect-square bg-gray-200 relative overflow-hidden">
                        @if($alumni->foto)
                            <img 
                                src="{{ $alumni->foto_url }}" 
                                alt="{{ $alumni->nama }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button 
                                wire:click="openDetailModal({{ $alumni->id }})"
                                class="px-6 py-3 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition"
                            >
                                Lihat Detail
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $alumni->nama }}</h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Lulus {{ $alumni->tahun_lulus }}</span>
                            </div>

                            @php
                                $badgeColors = [
                                    'kuliah' => 'bg-blue-100 text-blue-800',
                                    'kerja' => 'bg-green-100 text-green-800',
                                    'wirausaha' => 'bg-purple-100 text-purple-800',
                                    'belum_diketahui' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 {{ $badgeColors[$alumni->status_alumni] ?? 'bg-gray-100 text-gray-800' }} text-xs font-semibold rounded-full">
                                {{ $alumni->status_alumni_label }}
                            </span>

                            @if($alumni->institusi)
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $alumni->institusi }}</span>
                                </div>
                            @endif

                            @if($alumni->bidang)
                                <div class="text-sm text-gray-500 italic">{{ $alumni->bidang }}</div>
                            @endif
                        </div>

                        @if($alumni->deskripsi)
                            <div class="text-sm text-gray-600 line-clamp-2">
                                {{ $alumni->deskripsi_excerpt }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-lg">Belum ada data alumni yang dipublikasikan</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailAlumni)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/75" wire:click="closeDetailModal"></div>

                <div class="relative inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Detail Alumni</h3>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        {{-- Foto --}}
                        @if($detailAlumni->foto)
                            <div class="flex justify-center">
                                <img 
                                    src="{{ $detailAlumni->foto_url }}" 
                                    alt="{{ $detailAlumni->nama }}"
                                    class="w-48 h-48 rounded-full object-cover border-4 border-gray-200"
                                >
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="text-center">
                            <h4 class="text-2xl font-bold text-gray-900">{{ $detailAlumni->nama }}</h4>
                            <p class="text-gray-600 mt-2">Lulus {{ $detailAlumni->tahun_lulus }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-6 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status Alumni</p>
                                <p class="text-lg font-semibold">{{ $detailAlumni->status_alumni_label }}</p>
                            </div>
                            @if($detailAlumni->institusi)
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Institusi</p>
                                    <p class="text-lg font-semibold">{{ $detailAlumni->institusi }}</p>
                                </div>
                            @endif
                            @if($detailAlumni->bidang)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 mb-1">Bidang</p>
                                    <p class="text-lg font-semibold">{{ $detailAlumni->bidang }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        @if($detailAlumni->deskripsi)
                            <div>
                                <h5 class="text-lg font-bold text-gray-900 mb-3">Tentang</h5>
                                <div class="prose max-w-none">
                                    {!! $detailAlumni->deskripsi_html !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
