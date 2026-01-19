<div class="p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Struktur Organisasi RPL</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola data struktur organisasi dan tim pengajar jurusan RPL</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-4 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] transition font-medium flex items-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Anggota
            </button>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('message') }}
            </div>
        @endif

        {{-- List with Drag & Drop --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if(empty($struktur_list))
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada data. Tambahkan anggota pertama.</p>
                </div>
            @else
                <div 
                    x-data="{ 
                        items: {{ Js::from(collect($struktur_list)->map(fn($s) => ['id' => $s['id'], 'order' => $s['order']])) }},
                        dragging: null,
                        dragover: null
                    }"
                    class="divide-y divide-gray-200"
                >
                    @foreach($struktur_list as $item)
                        <div 
                            draggable="true"
                            @dragstart="dragging = {{ $item['id'] }}"
                            @dragend="dragging = null"
                            @dragover.prevent="dragover = {{ $item['id'] }}"
                            @dragleave="dragover = null"
                            @drop.prevent="
                                if (dragging !== {{ $item['id'] }}) {
                                    let dragIdx = items.findIndex(i => i.id === dragging);
                                    let dropIdx = items.findIndex(i => i.id === {{ $item['id'] }});
                                    let dragItem = items.splice(dragIdx, 1)[0];
                                    items.splice(dropIdx, 0, dragItem);
                                    items.forEach((item, idx) => item.order = idx);
                                    $wire.updateOrder(items);
                                }
                                dragging = null;
                                dragover = null;
                            "
                            :class="{ 'bg-blue-50': dragover === {{ $item['id'] }}, 'opacity-50': dragging === {{ $item['id'] }} }"
                            class="p-4 hover:bg-gray-50 transition cursor-move"
                        >
                            <div class="flex items-center space-x-4">
                                {{-- Drag Handle --}}
                                <div class="text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>

                                {{-- Order Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-700 font-semibold text-sm">
                                        {{ $item['order'] }}
                                    </span>
                                </div>

                                {{-- Foto 1:1 --}}
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $item['foto']) }}" alt="{{ $item['nama'] }}" class="h-16 w-16 object-cover rounded-full border-2 border-gray-200">
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $item['nama'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $item['jabatan'] }}</p>
                                    @if($item['deskripsi_md'])
                                        <p class="text-xs text-gray-500 truncate mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($item['deskripsi_md']), 60) }}</p>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item['status'] === 'published' ? 'Published' : 'Draft' }}
                                    </span>
                                </div>

                                {{-- Status Toggle --}}
                                <div class="flex-shrink-0">
                                    <button 
                                        wire:click="toggleStatus({{ $item['id'] }})"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#12B4E0] focus:ring-offset-2 {{ $item['status'] === 'published' ? 'bg-[#12B4E0]' : 'bg-gray-200' }}"
                                    >
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $item['status'] === 'published' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </div>

                                {{-- Actions --}}
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    <button 
                                        wire:click="openEditModal({{ $item['id'] }})"
                                        class="p-2 text-gray-400 hover:text-[#12B4E0] transition"
                                        title="Edit"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $item['id'] }})"
                                        wire:confirm="Yakin ingin menghapus data ini?"
                                        class="p-2 text-gray-400 hover:text-red-600 transition"
                                        title="Hapus"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $struktur_id ? 'Edit Anggota' : 'Tambah Anggota' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form 
                    @submit.prevent="
                        if (window.imageCropper && window.imageCropper.sendToLivewire) window.imageCropper.sendToLivewire();
                        $wire.save();
                    " 
                    class="p-6 space-y-6"
                >
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="nama"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            placeholder="Nama lengkap"
                        >
                        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="jabatan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            placeholder="Kepala Jurusan, Wakil, Guru, dll"
                        >
                        @error('jabatan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Order & Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            >
                            @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select 
                                wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>

                    {{-- Foto 1:1 with Interactive Cropper --}}
                    <x-image-cropper 
                        modelName="new_foto"
                        cropDataName="foto_crop_data"
                        label="Foto"
                        :aspectRatio="1"
                        aspectRatioLabel="1:1"
                        :maxWidth="1200"
                        :maxHeight="1200"
                        :required="true"
                        :currentImage="$foto"
                        :previewSize="200"
                    />

                    {{-- Markdown Editor Component --}}
                    <x-markdown-editor 
                        modelName="deskripsi_md"
                        label="Deskripsi (Markdown)"
                        placeholder="## Profil Singkat&#10;&#10;Pengalaman 15 tahun di bidang pendidikan teknologi.&#10;&#10;**Kompetensi:**&#10;- Manajemen Pendidikan&#10;- Pengembangan Kurikulum"
                        :rows="12"
                    />

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button 
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] transition font-medium flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $struktur_id ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
