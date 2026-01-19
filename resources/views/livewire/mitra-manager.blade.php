<div 
    x-data="{
        initSortable() {
            if (typeof Sortable !== 'undefined') {
                const tbody = document.querySelector('#sortable-mitra tbody');
                if (tbody) {
                    Sortable.create(tbody, {
                        animation: 150,
                        handle: '.drag-handle',
                        onEnd: (evt) => {
                            let items = [];
                            tbody.querySelectorAll('tr').forEach((row, index) => {
                                items.push({
                                    value: row.dataset.id,
                                    order: index + 1
                                });
                            });
                            @this.call('updateOrder', items);
                        }
                    });
                }
            }
        }
    }"
    x-init="
        if (typeof Sortable === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
            script.onload = () => initSortable();
            document.head.appendChild(script);
        } else {
            initSortable();
        }
    "
    class="p-6"
>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Mitra</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola logo mitra industri & kampus</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="px-4 py-2 rounded-lg font-medium text-white transition"
            style="background-color: {{ $globalSettings->primary_color_style }}"
        >
            + Tambah Mitra
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Cari nama mitra..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
        <div>
            <select
                wire:model.live="filterStatus"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table id="sortable-mitra" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Logo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mitra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Website</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mitras as $mitra)
                    <tr class="hover:bg-gray-50" data-id="{{ $mitra->id }}">
                        <td class="px-6 py-4">
                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($mitra->logo)
                                <img src="{{ asset('storage/' . $mitra->logo) }}" alt="{{ $mitra->nama_mitra }}" class="w-16 h-16 object-contain rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $mitra->nama_mitra }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($mitra->website)
                                <a href="{{ $mitra->website }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    {{ Str::limit($mitra->website, 30) }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $mitra->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($mitra->status === 'published')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button
                                wire:click="openEditModal({{ $mitra->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                            >
                                Edit
                            </button>
                            <button
                                wire:click="delete({{ $mitra->id }})"
                                wire:confirm="Yakin ingin menghapus mitra ini?"
                                class="text-red-600 hover:text-red-800 font-medium text-sm"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data mitra
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4">
                {{-- Backdrop --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"
                    wire:click="closeModal"
                ></div>

                {{-- Modal Content --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full z-50"
                >
                    <form
                        @submit.prevent="
                            if (window.imageCropper && window.imageCropper.sendToLivewire) window.imageCropper.sendToLivewire();
                            $wire.save();
                        "
                    >
                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $isEditMode ? 'Edit Mitra' : 'Tambah Mitra' }}
                            </h3>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                            {{-- Nama Mitra --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Mitra <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="nama_mitra"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: PT Gojek Indonesia"
                                >
                                @error('nama_mitra')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Logo --}}
                            <div>
                                <x-image-cropper
                                    modelName="logo"
                                    label="Logo Mitra"
                                    aspectRatio="1"
                                    aspectRatioLabel="1:1"
                                    :maxWidth="300"
                                    :maxHeight="300"
                                    :currentImage="$currentLogo"
                                    :previewSize="150"
                                />
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Website --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Website (Opsional)
                                </label>
                                <input
                                    type="url"
                                    wire:model="website"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="https://www.example.com"
                                >
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Order --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampil <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    wire:model="order"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                @error('order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-lg text-white font-medium transition"
                                style="background-color: {{ $globalSettings->primary_color_style }}"
                            >
                                {{ $isEditMode ? 'Perbarui' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
