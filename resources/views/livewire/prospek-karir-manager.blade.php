<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Prospek Karir</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola prospek karir lulusan jurusan RPL</p>
        </div>
        <button wire:click="openCreateModal" 
            class="px-4 py-2 bg-[#12B4E0] text-white font-medium rounded-lg hover:bg-[#0d8fb3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#12B4E0] transition-colors">
            + Tambah Prospek Karir
        </button>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Prospek Karir List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if (count($prospek_karirs) > 0)
            <div x-data="{
                items: @js($prospek_karirs),
                draggedItem: null,
                draggedIndex: null,
                
                dragStart(index) {
                    this.draggedIndex = index;
                    this.draggedItem = this.items[index];
                },
                
                dragEnd() {
                    this.draggedIndex = null;
                    this.draggedItem = null;
                },
                
                dragOver(index) {
                    if (this.draggedIndex === null || this.draggedIndex === index) return;
                    
                    const draggedItem = this.items.splice(this.draggedIndex, 1)[0];
                    this.items.splice(index, 0, draggedItem);
                    this.draggedIndex = index;
                    
                    this.updateOrder();
                },
                
                updateOrder() {
                    const reordered = this.items.map((item, index) => ({
                        id: item.id,
                        order: index
                    }));
                    @this.call('updateOrder', reordered);
                }
            }">
                <template x-for="(item, index) in items" :key="item.id">
                    <div 
                        draggable="true"
                        @dragstart="dragStart(index)"
                        @dragend="dragEnd()"
                        @dragover.prevent="dragOver(index)"
                        :class="{ 'opacity-50': draggedIndex === index }"
                        class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors cursor-move">
                        
                        <div class="p-4 flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="flex-shrink-0 text-gray-400 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </div>

                            <!-- Order Badge -->
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#12B4E0] text-white text-sm font-medium" x-text="index + 1"></span>
                            </div>

                            <!-- Image Preview -->
                            <div class="flex-shrink-0" x-show="item.image">
                                <img :src="'/storage/' + item.image" :alt="item.title" class="w-20 h-20 object-cover rounded-lg">
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="item.title"></h3>
                                    <span x-show="item.icon" class="text-xl" x-text="item.icon"></span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600 line-clamp-2" x-html="item.description.substring(0, 150) + (item.description.length > 150 ? '...' : '')"></p>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0 flex items-center gap-3">
                                <!-- Status Toggle -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                        :checked="item.is_active"
                                        @click="$wire.toggleActive(item.id)"
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#12B4E0]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#12B4E0]"></div>
                                </label>

                                <!-- Edit Button -->
                                <button @click="$wire.openEditModal(item.id)" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button @click="if(confirm('Yakin ingin menghapus prospek karir ini?')) $wire.delete(item.id)" 
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada prospek karir</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan prospek karir pertama.</p>
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" @click="$wire.closeModal()"></div>

                <!-- Modal Content -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="save">
                        <!-- Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $prospek_karir_id ? 'Edit Prospek Karir' : 'Tambah Prospek Karir' }}
                            </h3>
                        </div>

                        <!-- Body -->
                        <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="title" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                    placeholder="Contoh: Full Stack Developer">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Icon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Icon (Emoji)
                                </label>
                                <input type="text" wire:model="icon" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                    placeholder="Contoh: 💻 🎨 📱">
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Opsional. Gunakan emoji untuk visual yang menarik.</p>
                            </div>

                            <!-- Description (Markdown) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="description" rows="6"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent font-mono text-sm"
                                    placeholder="Deskripsi prospek karir (mendukung Markdown)..."></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Mendukung format Markdown: **bold**, *italic*, - list, dll.</p>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar
                                </label>
                                
                                @if ($image && !$new_image)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($image) }}" alt="Current" 
                                            class="w-48 h-36 object-cover rounded-lg border border-gray-200">
                                    </div>
                                @endif

                                @if ($new_image)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 mb-2">Preview baru:</p>
                                        <img src="{{ $new_image->temporaryUrl() }}" alt="Preview" 
                                            class="w-48 h-36 object-cover rounded-lg border border-gray-200">
                                    </div>
                                @endif

                                <input type="file" wire:model="new_image" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#12B4E0] file:text-white hover:file:bg-[#0d8fb3] cursor-pointer">
                                
                                @error('new_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <p class="mt-1 text-xs text-gray-500">Maksimal 5MB. Akan diproses menjadi 800x600px WebP.</p>
                            </div>

                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="order" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent">
                                @error('order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Is Active -->
                            <div>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-[#12B4E0] border-gray-300 rounded focus:ring-[#12B4E0]">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Aktif</span>
                                </label>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" @click="$wire.closeModal()" 
                                class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                class="px-6 py-2 bg-[#12B4E0] text-white font-medium rounded-lg hover:bg-[#0d8fb3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#12B4E0] transition-colors">
                                <span wire:loading.remove wire:target="save">Simpan</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
