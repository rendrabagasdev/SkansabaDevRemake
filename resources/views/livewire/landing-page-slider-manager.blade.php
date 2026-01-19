<div class="p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Landing Page Slider</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola gambar slider di hero section landing page</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-4 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] transition font-medium"
            >
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Slider
            </button>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        {{-- Sliders List with Drag & Drop --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($sliders->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada slider. Tambahkan slider pertama Anda.</p>
                </div>
            @else
                <div 
                    x-data="{ 
                        items: {{ Js::from($sliders->map(fn($s) => ['id' => $s->id, 'order' => $s->order])) }},
                        dragging: null,
                        dragover: null
                    }"
                    class="divide-y divide-gray-200"
                >
                    @foreach($sliders as $slider)
                        <div 
                            draggable="true"
                            @dragstart="dragging = {{ $slider->id }}"
                            @dragend="dragging = null"
                            @dragover.prevent="dragover = {{ $slider->id }}"
                            @dragleave="dragover = null"
                            @drop.prevent="
                                if (dragging !== {{ $slider->id }}) {
                                    let dragIdx = items.findIndex(i => i.id === dragging);
                                    let dropIdx = items.findIndex(i => i.id === {{ $slider->id }});
                                    let dragItem = items.splice(dragIdx, 1)[0];
                                    items.splice(dropIdx, 0, dragItem);
                                    items.forEach((item, idx) => item.order = idx);
                                    $wire.updateOrder(items);
                                }
                                dragging = null;
                                dragover = null;
                            "
                            :class="{ 'bg-blue-50': dragover === {{ $slider->id }}, 'opacity-50': dragging === {{ $slider->id }} }"
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
                                        {{ $slider->order }}
                                    </span>
                                </div>

                                {{-- Image Preview --}}
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="h-16 w-28 object-cover rounded-lg">
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $slider->title }}</h3>
                                    @if($slider->subtitle)
                                        <p class="text-sm text-gray-500 truncate">{{ $slider->subtitle }}</p>
                                    @endif
                                    @if($slider->link)
                                        <a href="{{ $slider->link }}" target="_blank" class="text-xs text-[#12B4E0] hover:underline truncate block">
                                            {{ $slider->link }}
                                        </a>
                                    @endif
                                </div>

                                {{-- Status Toggle --}}
                                <div class="flex-shrink-0">
                                    <button 
                                        wire:click="toggleActive({{ $slider->id }})"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#12B4E0] focus:ring-offset-2"
                                        :class="{'bg-[#12B4E0]': {{ $slider->is_active ? 'true' : 'false' }}, 'bg-gray-200': {{ $slider->is_active ? 'false' : 'true' }}}"
                                    >
                                        <span 
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                            :class="{'translate-x-6': {{ $slider->is_active ? 'true' : 'false' }}, 'translate-x-1': {{ $slider->is_active ? 'false' : 'true' }}}"
                                        ></span>
                                    </button>
                                </div>

                                {{-- Actions --}}
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    <button 
                                        wire:click="openEditModal({{ $slider->id }})"
                                        class="p-2 text-gray-400 hover:text-[#12B4E0] transition"
                                        title="Edit"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $slider->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus slider ini?"
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
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $isEditing ? 'Edit Slider' : 'Tambah Slider' }}
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
                    {{-- Image Cropper 16:9 --}}
                    <x-image-cropper 
                        modelName="new_image"
                        label="Gambar Slider"
                        aspectRatio="1.91"
                        aspectRatioLabel="Cinematic rasio "
                        :maxWidth="1920"
                        :maxHeight="1080"
                        :required="true"
                        :currentImage="$currentImage"
                        :previewSize="350"
                    />

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            placeholder="Masukkan judul slider"
                        >
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Subtitle --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                        <input 
                            type="text" 
                            wire:model="subtitle"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            placeholder="Masukkan subtitle (opsional)"
                        >
                        @error('subtitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Link --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link</label>
                        <input 
                            type="url" 
                            wire:model="link"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                            placeholder="https://example.com"
                        >
                        @error('link') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Order & Active --}}
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
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="is_active"
                                    class="rounded border-gray-300 text-[#12B4E0] focus:ring-[#12B4E0]"
                                >
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button 
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] transition font-medium"
                        >
                            {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
