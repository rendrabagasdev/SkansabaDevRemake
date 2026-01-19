<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Galeri</h1>
                <p class="text-gray-600 mt-1">Dokumentasi visual kegiatan siswa dan jurusan RPL</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Galeri
            </button>
        </div>

        {{-- Flash Messages --}}
        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Cari judul galeri..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                    >
                </div>
                
                <div>
                    <select wire:model.live="filterKategori" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Kategori</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="lomba">Lomba</option>
                        <option value="pembelajaran">Pembelajaran</option>
                        <option value="kunjungan">Kunjungan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Grid Gallery --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($galeriList as $item)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition group">
                    {{-- Image --}}
                    <div class="relative aspect-video overflow-hidden bg-gray-200">
                        @if($item->gambar)
                            <img 
                                src="{{ Storage::url($item->gambar) }}" 
                                alt="{{ $item->judul }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Category Badge --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->kategori_badge_color }}">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </div>

                        {{-- Status Badge --}}
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($item->status === 'published') bg-green-100 text-green-800
                                @elseif($item->status === 'draft') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $item->judul }}</h3>
                        
                        @if($item->deskripsi)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($item->deskripsi), 80) }}</p>
                        @endif

                        @if($item->published_at)
                            <p class="text-xs text-gray-500 mb-3">{{ $item->published_at->format('d M Y') }}</p>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                            <button 
                                wire:click="openDetailModal({{ $item->id }})"
                                class="flex-1 px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition font-medium"
                            >
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </button>
                            <button 
                                wire:click="openEditModal({{ $item->id }})"
                                class="flex-1 px-3 py-2 text-sm text-yellow-600 hover:bg-yellow-50 rounded-lg transition font-medium"
                            >
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            <button 
                                wire:click="delete({{ $item->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus galeri ini?"
                                class="px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada galeri</p>
                    <p class="text-sm text-gray-500 mt-1">Tambahkan foto dokumentasi kegiatan jurusan</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeModal"></div>

                <div class="relative inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $isEditMode ? 'Edit Galeri' : 'Tambah Galeri Baru' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form 
                        @submit.prevent="
                            if (window.imageCropper && window.imageCropper.sendToLivewire) 
                                window.imageCropper.sendToLivewire();
                            $wire.save();
                        "
                    >
                        {{-- Judul --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Galeri *</label>
                            <input 
                                type="text" 
                                wire:model="judul" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                placeholder="Contoh: Kunjungan Industri ke PT. Pertamina"
                            >
                            @error('judul') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
                            <select 
                                wire:model="kategori" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                            >
                                <option value="kegiatan">Kegiatan</option>
                                <option value="lomba">Lomba</option>
                                <option value="pembelajaran">Pembelajaran</option>
                                <option value="kunjungan">Kunjungan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('kategori') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Image Cropper --}}
                        <div class="mb-6">
                            <x-image-cropper 
                                modelName="gambar"
                                label="Gambar Galeri (16:9)"
                                aspectRatio="16/9"
                                aspectRatioLabel="16:9"
                                :maxWidth="1600"
                                :maxHeight="900"
                                :currentImage="$currentGambar"
                                :previewSize="200"
                                :required="!$isEditMode"
                            />
                            @error('gambar') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Deskripsi Markdown --}}
                        <div class="mb-6">
                            <x-markdown-editor 
                                modelName="deskripsi"
                                label="Deskripsi (Opsional)"
                                placeholder="## Detail Galeri&#10;&#10;Tuliskan deskripsi lengkap kegiatan atau dokumentasi...&#10;&#10;**Highlights:**&#10;- Point penting 1&#10;- Point penting 2"
                                :rows="10"
                                :required="false"
                            />
                            @error('deskripsi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Publikasi *</label>
                            <select 
                                wire:model="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit"
                                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg font-semibold hover:bg-[#0e91b8] transition"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEditMode ? 'Perbarui' : 'Simpan' }}
                                </span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailGaleri)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeDetailModal"></div>

                <div class="relative inline-block w-full max-w-4xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    {{-- Image --}}
                    <div class="relative aspect-video overflow-hidden bg-gray-900">
                        @if($detailGaleri->gambar)
                            <img 
                                src="{{ Storage::url($detailGaleri->gambar) }}" 
                                alt="{{ $detailGaleri->judul }}" 
                                class="w-full h-full object-contain"
                            >
                        @endif

                        {{-- Close Button --}}
                        <button 
                            wire:click="closeDetailModal" 
                            class="absolute top-4 right-4 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Badges --}}
                        <div class="absolute bottom-4 left-4 flex gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $detailGaleri->kategori_badge_color }}">
                                {{ ucfirst($detailGaleri->kategori) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($detailGaleri->status === 'published') bg-green-100 text-green-800
                                @elseif($detailGaleri->status === 'draft') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($detailGaleri->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-8 py-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $detailGaleri->judul }}</h2>
                        
                        <div class="flex items-center gap-6 text-sm text-gray-600 mb-6">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $detailGaleri->user->name }}
                            </span>
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $detailGaleri->created_at->format('d F Y') }}
                            </span>
                            @if($detailGaleri->published_at)
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Dipublikasi: {{ $detailGaleri->published_at->format('d M Y') }}
                                </span>
                            @endif
                        </div>

                        @if($detailGaleri->deskripsi)
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! $detailGaleri->deskripsi_html !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
