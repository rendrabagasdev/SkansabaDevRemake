<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Fasilitas</h1>
                <p class="text-gray-600 mt-1">Manajemen data fasilitas jurusan RPL</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Fasilitas
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Cari tempat/ruangan..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                    >
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

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('tempat')">
                                Tempat/Ruangan
                                @if($sortField === 'tempat')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fasilitas</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($fasilitasList as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    @if($item->gambar)
                                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->tempat }}" class="w-24 h-14 object-cover rounded-lg">
                                    @else
                                        <div class="w-24 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $item->tempat }}</div>
                                    @if($item->deskripsi)
                                        <div class="text-sm text-gray-600 mt-1">{{ Str::limit($item->deskripsi, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if($item->fasilitas && count($item->fasilitas) > 0)
                                            @foreach(array_slice($item->fasilitas, 0, 3) as $fas)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#12B4E0] text-white">
                                                    {{ $fas }}
                                                </span>
                                            @endforeach
                                            @if(count($item->fasilitas) > 3)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ count($item->fasilitas) - 3 }} lagi
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400 italic">Belum ada fasilitas</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($item->status === 'published') bg-green-100 text-green-800
                                        @elseif($item->status === 'draft') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button 
                                            wire:click="openDetailModal({{ $item->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="openEditModal({{ $item->id }})"
                                            class="text-yellow-600 hover:text-yellow-800 transition"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="delete({{ $item->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus fasilitas ini?"
                                            class="text-red-600 hover:text-red-800 transition"
                                            title="Hapus"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data fasilitas</p>
                                    <p class="text-sm mt-1">Klik tombol "Tambah Fasilitas" untuk menambahkan data baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-5xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $isEditMode ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form 
                    @submit.prevent="
                        if (window.imageCropper && window.imageCropper.sendToLivewire) window.imageCropper.sendToLivewire();
                        $wire.save();
                    "
                    id="fasilitasForm"
                >
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Left Column --}}
                            <div class="space-y-6">
                                {{-- Tempat --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tempat/Ruangan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="tempat"
                                        placeholder="contoh: Lab RPL 1, Lab 19"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent @error('tempat') border-red-500 @enderror"
                                    >
                                    @error('tempat') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        wire:model="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] @error('status') border-red-500 @enderror"
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                    @error('status') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                {{-- Gambar Upload --}}
                                <div>
                                    <x-image-cropper 
                                        modelName="gambar"
                                        label="Gambar Ruangan"
                                        :aspectRatio="16/9"
                                        aspectRatioLabel="16:9"
                                        :maxWidth="1600"
                                        :maxHeight="900"
                                        :currentImage="$currentGambar"
                                        :previewSize="300"
                                    />
                                    @error('gambar') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                {{-- Deskripsi (Markdown) --}}
                                <div>
                                    <x-markdown-editor
                                        modelName="deskripsi"
                                        label="Deskripsi Ruangan"
                                        placeholder="## Kondisi Ruangan&#10;&#10;Ruangan dalam kondisi baik dengan pencahayaan yang memadai.&#10;&#10;**Catatan:**&#10;- Suhu ruangan sejuk&#10;- Kebersihan terjaga"
                                        :rows="10"
                                        :required="false"
                                    />
                                    @error('deskripsi') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            {{-- Right Column: Badge Repeater --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Daftar Fasilitas
                                </label>
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    {{-- Input untuk menambahkan item --}}
                                    <div class="flex gap-2 mb-4">
                                        <input 
                                            type="text" 
                                            wire:model="newFasilitasItem"
                                            wire:keydown.enter.prevent="addFasilitasItem"
                                            placeholder="Ketik fasilitas (contoh: Laptop ROG x19)"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                        >
                                        <button 
                                            type="button"
                                            wire:click="addFasilitasItem"
                                            class="px-4 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-1 transition"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Tambah
                                        </button>
                                    </div>

                                    @error('newFasilitasItem') 
                                        <span class="text-red-500 text-sm mb-2 block">{{ $message }}</span> 
                                    @enderror

                                    {{-- Badge List --}}
                                    <div class="flex flex-wrap gap-2 min-h-[100px]">
                                        @forelse($fasilitas as $index => $item)
                                            <div class="inline-flex items-center px-3 py-1.5 bg-[#12B4E0] text-white rounded-full text-sm font-medium gap-2 group hover:bg-[#0e91b8] transition">
                                                <span>{{ $item }}</span>
                                                <button 
                                                    type="button"
                                                    wire:click="removeFasilitasItem({{ $index }})"
                                                    class="text-white hover:text-red-200 transition"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @empty
                                            <div class="w-full text-center text-gray-400 italic py-8">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                                <p>Belum ada fasilitas ditambahkan</p>
                                                <p class="text-xs mt-1">Ketik nama fasilitas dan klik tombol "Tambah"</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- Example Hints --}}
                                    <div class="mt-4 pt-4 border-t border-gray-300">
                                        <p class="text-xs text-gray-600 font-semibold mb-2">Contoh fasilitas:</p>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">Laptop ROG x19</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">AC x5</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">Proyektor</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">Router Mikrotik</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">Meja x20</span>
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs">Kursi x40</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                        <button 
                            type="button"
                            wire:click="closeModal"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold transition"
                        >
                            {{ $isEditMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailFasilitas)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                {{-- Header dengan Gradient --}}
                <div class="relative h-48 bg-gradient-to-r from-[#12B4E0] to-[#0e91b8] rounded-t-lg">
                    @if($detailFasilitas->gambar)
                        <img src="{{ Storage::url($detailFasilitas->gambar) }}" alt="{{ $detailFasilitas->tempat }}" class="w-full h-full object-cover rounded-t-lg opacity-20">
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center">
                        <h2 class="text-3xl font-bold text-white text-center px-4">{{ $detailFasilitas->tempat }}</h2>
                    </div>
                    <button wire:click="closeDetailModal" class="absolute top-4 right-4 text-white hover:text-gray-200 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Gambar Ruangan --}}
                    @if($detailFasilitas->gambar)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Foto Ruangan</h3>
                            <img src="{{ Storage::url($detailFasilitas->gambar) }}" alt="{{ $detailFasilitas->tempat }}" class="w-full rounded-lg shadow-lg">
                        </div>
                    @endif

                    {{-- Status --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Status</h3>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                            @if($detailFasilitas->status === 'published') bg-green-100 text-green-800
                            @elseif($detailFasilitas->status === 'draft') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($detailFasilitas->status) }}
                        </span>
                    </div>

                    {{-- Deskripsi --}}
                    @if($detailFasilitas->deskripsi)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi</h3>
                            <div class="prose prose-sm max-w-none">
                                {!! $detailFasilitas->deskripsi_html !!}
                            </div>
                        </div>
                    @endif

                    {{-- Daftar Fasilitas --}}
                    @if($detailFasilitas->fasilitas && count($detailFasilitas->fasilitas) > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                Daftar Fasilitas 
                                <span class="text-sm text-gray-500 font-normal">({{ $detailFasilitas->fasilitas_count }} item)</span>
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($detailFasilitas->fasilitas as $fas)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-[#12B4E0] text-white">
                                        {{ $fas }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Metadata --}}
                    <div class="border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Dibuat oleh:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ $detailFasilitas->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Dibuat pada:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ $detailFasilitas->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($detailFasilitas->published_at)
                                <div>
                                    <span class="text-gray-600">Dipublikasi:</span>
                                    <span class="font-semibold text-gray-900 ml-2">{{ $detailFasilitas->published_at->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3 rounded-b-lg">
                    <button 
                        wire:click="closeDetailModal"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
