<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Karya Siswa</h1>
                <p class="text-gray-600 mt-1">Manajemen data karya siswa RPL</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Karya
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Cari judul, siswa, teknologi..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                    >
                </div>
                
                <div>
                    <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="review">Review</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterKategori" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Kategori</option>
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="desktop">Desktop</option>
                        <option value="game">Game</option>
                        <option value="iot">IoT</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterTahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('judul')">
                                Judul
                                @if($sortField === 'judul')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teknologi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($karyaSiswas as $karya)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="font-semibold text-gray-900">{{ $karya->judul }}</p>
                                        <p class="text-sm text-gray-600">{{ $karya->kelas }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900">{{ $karya->nama_siswa }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ ucfirst($karya->kategori) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $karya->teknologi }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-sm font-medium rounded-full
                                        @if($karya->status === 'draft') bg-gray-100 text-gray-800
                                        @elseif($karya->status === 'review') bg-yellow-100 text-yellow-800
                                        @elseif($karya->status === 'published') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($karya->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $karya->tahun }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button 
                                            wire:click="openDetailModal({{ $karya->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-semibold"
                                        >
                                            Detail
                                        </button>
                                        <button 
                                            wire:click="openEditModal({{ $karya->id }})"
                                            class="text-[#12B4E0] hover:text-[#0e91b8] font-semibold"
                                        >
                                            Edit
                                        </button>
                                        @if($karya->trashed())
                                            <button 
                                                wire:click="restore({{ $karya->id }})"
                                                wire:confirm="Pulihkan karya ini?"
                                                class="text-blue-600 hover:text-blue-800 font-semibold"
                                            >
                                                Pulihkan
                                            </button>
                                        @else
                                            <button 
                                                wire:click="delete({{ $karya->id }})"
                                                wire:confirm="Hapus karya ini?"
                                                class="text-red-600 hover:text-red-800 font-semibold"
                                            >
                                                Hapus
                                            </button>
                                        @endif
                                        @if($karya->trashed())
                                            <button 
                                                wire:click="forceDelete({{ $karya->id }})"
                                                wire:confirm="Hapus permanen? Tindakan ini tidak dapat dibatalkan."
                                                class="text-red-800 hover:text-red-900 font-semibold"
                                            >
                                                Hapus Permanen
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-lg font-semibold">Tidak ada data karya siswa</p>
                                    <p class="text-sm mt-1">Klik tombol "Tambah Karya" untuk membuat data baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal --}}
        @if($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900/75" @click="$wire.closeModal()"></div>

                    <div class="relative inline-block w-full max-w-5xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-50">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $isEditMode ? 'Edit Karya Siswa' : 'Tambah Karya Siswa Baru' }}
                            </h3>
                            <button @click="$wire.closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                            <form 
                                @submit.prevent="
                                    if (window.imageCropper && window.imageCropper.sendToLivewire) window.imageCropper.sendToLivewire();
                                    $wire.save();
                                " 
                                id="karyaSiswaForm"
                            >
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {{-- Left Column --}}
                                    <div class="space-y-6">
                                        {{-- Judul --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Judul Karya <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="judul"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                placeholder="Contoh: E-Commerce Platform"
                                            >
                                            @error('judul') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Nama Siswa & Kelas --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Nama Siswa <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    wire:model="nama_siswa"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                @error('nama_siswa') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Kelas <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    wire:model="kelas"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                    placeholder="Contoh: XII RPL 1"
                                                >
                                                @error('kelas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        {{-- Kategori & Teknologi --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Kategori <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    wire:model="kategori"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="web">Web</option>
                                                    <option value="mobile">Mobile</option>
                                                    <option value="desktop">Desktop</option>
                                                    <option value="game">Game</option>
                                                    <option value="iot">IoT</option>
                                                    <option value="lainnya">Lainnya</option>
                                                </select>
                                                @error('kategori') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Teknologi <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    wire:model="teknologi"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                    placeholder="Contoh: React, Laravel"
                                                >
                                                @error('teknologi') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        {{-- Tahun & Status --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tahun <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    wire:model="tahun"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                    @foreach($years as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                                @error('tahun') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Status <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    wire:model="status"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                    <option value="draft">Draft</option>
                                                    <option value="review">Review</option>
                                                    <option value="published">Published</option>
                                                    <option value="archived">Archived</option>
                                                </select>
                                                @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        {{-- URL Demo & Repo --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    URL Demo
                                                </label>
                                                <input 
                                                    type="url" 
                                                    wire:model="url_demo"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                    placeholder="https://example.com"
                                                >
                                                @error('url_demo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    URL Repository
                                                </label>
                                                <input 
                                                    type="url" 
                                                    wire:model="url_repo"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                    placeholder="https://github.com/..."
                                                >
                                                @error('url_repo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right Column --}}
                                    <div class="space-y-6">
                                        {{-- Image Cropper (16:9 ratio) --}}
                                        <x-image-cropper
                                            modelName="gambar"
                                            label="Screenshot Karya"
                                            :aspectRatio="16/9"
                                            aspectRatioLabel="16:9"
                                            :maxWidth="1920"
                                            :maxHeight="1080"
                                            :currentImage="$currentGambar"
                                            :previewSize="200"
                                        />

                                        {{-- Markdown Editor --}}
                                        <x-markdown-editor
                                            modelName="deskripsi"
                                            label="Deskripsi Karya"
                                            placeholder="## Deskripsi Karya&#10;&#10;Jelaskan fitur utama, teknologi yang digunakan, dan tantangan yang dihadapi.&#10;&#10;**Fitur Utama:**&#10;- Feature 1&#10;- Feature 2"
                                            :rows="12"
                                            :required="true"
                                        />
                                    </div>
                                </div>

                                {{-- Modal Footer (buttons) --}}
                                <div class="mt-8 flex items-center justify-end gap-3 bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                                    <button 
                                        type="button"
                                        @click="$wire.closeModal()"
                                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit"
                                        class="px-6 py-2 bg-[#12B4E0] text-white rounded-lg font-semibold hover:bg-[#0e91b8] transition flex items-center gap-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Karya' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Modal --}}
        @if($showDetailModal && $detailKarya)
            <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetailModal') }">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900/75" @click="$wire.closeDetailModal()"></div>

                    <div class="relative inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-50">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-[#12B4E0] to-[#0e91b8]">
                            <h3 class="text-xl font-bold text-white">
                                Detail Karya Siswa
                            </h3>
                            <button @click="$wire.closeDetailModal()" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                            <div class="space-y-6">
                                {{-- Title --}}
                                <div>
                                    <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $detailKarya->judul }}</h2>
                                    <div class="flex items-center gap-3 text-sm text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $detailKarya->nama_siswa }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $detailKarya->kelas }}</span>
                                        <span>•</span>
                                        <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                            {{ ucfirst($detailKarya->kategori) }}
                                        </span>
                                        <span>•</span>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            @if($detailKarya->status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($detailKarya->status === 'review') bg-yellow-100 text-yellow-800
                                            @elseif($detailKarya->status === 'published') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif
                                        ">
                                            {{ ucfirst($detailKarya->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Image --}}
                                @if($detailKarya->gambar)
                                    <div class="rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                        <img 
                                            src="{{ asset('storage/' . $detailKarya->gambar) }}" 
                                            alt="{{ $detailKarya->judul }}"
                                            class="w-full h-auto object-cover"
                                        >
                                    </div>
                                @endif

                                {{-- Metadata --}}
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Tahun</p>
                                        <p class="text-lg font-bold text-gray-900">{{ $detailKarya->tahun }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Teknologi</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $detailKarya->teknologi }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Dibuat oleh</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $detailKarya->user->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Tanggal</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $detailKarya->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>

                                {{-- Links --}}
                                @if($detailKarya->url_demo || $detailKarya->url_repo)
                                    <div class="flex items-center gap-3">
                                        @if($detailKarya->url_demo)
                                            <a 
                                                href="{{ $detailKarya->url_demo }}" 
                                                target="_blank"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] transition font-semibold"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                Lihat Demo
                                            </a>
                                        @endif
                                        @if($detailKarya->url_repo)
                                            <a 
                                                href="{{ $detailKarya->url_repo }}" 
                                                target="_blank"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition font-semibold"
                                            >
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                                </svg>
                                                Repository
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                {{-- Description --}}
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">Deskripsi</h3>
                                    <div class="prose prose-sm max-w-none bg-white border border-gray-200 rounded-lg p-4">
                                        {!! $detailKarya->deskripsi_html !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <button 
                                type="button"
                                @click="$wire.closeDetailModal()"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
