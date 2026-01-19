<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Dokumen Kurikulum</h1>
                <p class="text-gray-600 mt-1">Manajemen dokumen kurikulum, silabus, dan modul pengajaran</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Dokumen
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
                        placeholder="Cari judul dokumen..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                    >
                </div>
                
                <div>
                    <select wire:model.live="filterJenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Jenis</option>
                        <option value="kurikulum">Kurikulum</option>
                        <option value="silabus">Silabus</option>
                        <option value="modul">Modul</option>
                        <option value="panduan">Panduan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <input 
                        type="number" 
                        wire:model.live="filterTahun" 
                        placeholder="Filter tahun..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('judul')">
                                Judul Dokumen
                                @if($sortField === 'judul')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('tahun_berlaku')">
                                Tahun Berlaku
                                @if($sortField === 'tahun_berlaku')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($dokumenList as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $item->judul }}</div>
                                    @if($item->published_at)
                                        <div class="text-xs text-gray-500 mt-1">Dipublikasi: {{ $item->published_at->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->jenis_badge_color }}">
                                        {{ ucfirst($item->jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">{{ $item->tahun_berlaku }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $item->file_extension }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $item->formatted_file_size }}</span>
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
                                        @if($item->status === 'published')
                                            <a 
                                                href="{{ $item->file_url }}"
                                                download
                                                class="text-green-600 hover:text-green-800 transition"
                                                title="Download"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                        @endif
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
                                            wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
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
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada dokumen</p>
                                    <p class="text-sm mt-1">Tambahkan dokumen kurikulum, silabus, atau modul</p>
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
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeModal"></div>

                <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen Baru' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        {{-- Judul --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Dokumen *</label>
                            <input 
                                type="text" 
                                wire:model="judul" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                placeholder="Contoh: Kurikulum Merdeka RPL 2024"
                            >
                            @error('judul') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Jenis & Tahun --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Dokumen *</label>
                                <select 
                                    wire:model="jenis" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                >
                                    <option value="kurikulum">Kurikulum</option>
                                    <option value="silabus">Silabus</option>
                                    <option value="modul">Modul</option>
                                    <option value="panduan">Panduan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                @error('jenis') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Berlaku *</label>
                                <input 
                                    type="number" 
                                    wire:model="tahun_berlaku" 
                                    min="2000" 
                                    max="{{ date('Y') + 10 }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                    placeholder="{{ date('Y') }}"
                                >
                                @error('tahun_berlaku') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                File Dokumen {{ !$isEditMode ? '*' : '(Opsional untuk update)' }}
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#12B4E0] transition">
                                <input 
                                    type="file" 
                                    wire:model="file" 
                                    accept=".pdf,.doc,.docx"
                                    class="hidden"
                                    id="fileInput"
                                >
                                <label for="fileInput" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm text-gray-600 font-medium">Klik untuk pilih file</p>
                                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX (Max 10MB)</p>
                                    
                                    @if($file)
                                        <div class="mt-4 inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm text-green-700 font-medium">{{ $file->getClientOriginalName() }}</span>
                                        </div>
                                    @elseif($currentFile)
                                        <div class="mt-4 text-sm text-gray-600">
                                            File saat ini: <span class="font-medium">{{ basename($currentFile) }}</span>
                                        </div>
                                    @endif
                                </label>
                            </div>
                            @error('file') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-2">⚠️ File akan tersedia untuk diunduh publik setelah dipublikasi</p>
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
    @if($showDetailModal && $detailDokumen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeDetailModal"></div>

                <div class="relative inline-block w-full max-w-3xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    {{-- Header with gradient --}}
                    <div class="bg-gradient-to-r from-[#12B4E0] to-[#0e91b8] px-8 py-6 text-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold mb-2">{{ $detailDokumen->judul }}</h3>
                                <div class="flex items-center gap-4 text-sm text-white/90">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $detailDokumen->user->name }}
                                    </span>
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $detailDokumen->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                            <button wire:click="closeDetailModal" class="text-white/90 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-8 py-6">
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Jenis Dokumen</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $detailDokumen->jenis_badge_color }}">
                                    {{ ucfirst($detailDokumen->jenis) }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Tahun Berlaku</label>
                                <p class="text-gray-900 font-semibold">{{ $detailDokumen->tahun_berlaku }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($detailDokumen->status === 'published') bg-green-100 text-green-800
                                    @elseif($detailDokumen->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($detailDokumen->status) }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Ukuran File</label>
                                <p class="text-gray-900 font-semibold">{{ $detailDokumen->formatted_file_size }} ({{ $detailDokumen->file_extension }})</p>
                            </div>
                        </div>

                        @if($detailDokumen->published_at)
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Dipublikasi</label>
                                <p class="text-gray-900">{{ $detailDokumen->published_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        @endif

                        {{-- Download Button --}}
                        @if($detailDokumen->status === 'published')
                            <div class="border-t border-gray-200 pt-6">
                                <a 
                                    href="{{ $detailDokumen->file_url }}"
                                    download
                                    class="inline-flex items-center px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold transition"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download Dokumen
                                </a>
                            </div>
                        @else
                            <div class="border-t border-gray-200 pt-6">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Catatan:</strong> Dokumen ini belum dipublikasi dan tidak dapat diunduh publik.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
