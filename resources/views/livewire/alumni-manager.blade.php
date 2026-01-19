<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Alumni</h1>
                <p class="text-gray-600 mt-1">Data alumni jurusan RPL dan kelanjutan studi/karier mereka</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Alumni
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
                        placeholder="Cari nama/institusi..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                    >
                </div>
                
                <div>
                    <select wire:model.live="filterTahunLulus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 1990; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterStatusAlumni" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Status Alumni</option>
                        <option value="kuliah">Kuliah</option>
                        <option value="kerja">Kerja</option>
                        <option value="wirausaha">Wirausaha</option>
                        <option value="belum_diketahui">Belum Diketahui</option>
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

        {{-- Alumni Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($alumniList as $alumni)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    {{-- Foto --}}
                    <div class="aspect-square bg-gray-200 relative overflow-hidden">
                        @if($alumni->foto)
                            <img 
                                src="{{ $alumni->foto_url }}" 
                                alt="{{ $alumni->nama }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Status Badges --}}
                        <div class="absolute top-3 right-3 flex gap-2">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-500',
                                    'published' => 'bg-green-500',
                                    'archived' => 'bg-red-500'
                                ];
                            @endphp
                            <span class="px-3 py-1 {{ $statusColors[$alumni->status] ?? 'bg-gray-500' }} text-white text-xs font-semibold rounded-full">
                                {{ ucfirst($alumni->status) }}
                            </span>
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $alumni->institusi }}</span>
                                </div>
                            @endif

                            @if($alumni->bidang)
                                <div class="text-sm text-gray-500 italic">{{ $alumni->bidang }}</div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                            <button 
                                wire:click="openDetailModal({{ $alumni->id }})"
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition"
                            >
                                Detail
                            </button>
                            <button 
                                wire:click="openEditModal({{ $alumni->id }})"
                                class="flex-1 px-4 py-2 bg-[#12B4E0] hover:bg-[#0e91b8] text-white rounded-lg text-sm font-semibold transition"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="delete({{ $alumni->id }})"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data alumni ini?')"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold transition"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-lg">Belum ada data alumni</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeModal"></div>

                <div class="relative inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $isEditMode ? 'Edit Alumni' : 'Tambah Alumni Baru' }}
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Left Column --}}
                            <div class="space-y-4">
                                {{-- Nama --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Alumni *</label>
                                    <input 
                                        type="text" 
                                        wire:model="nama" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                        placeholder="Nama lengkap alumni"
                                    >
                                    @error('nama') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Tahun Lulus --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Lulus *</label>
                                    <select 
                                        wire:model="tahun_lulus" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                    >
                                        @for($year = date('Y') + 1; $year >= 1990; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('tahun_lulus') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Status Alumni --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Alumni *</label>
                                    <select 
                                        wire:model="status_alumni" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                    >
                                        <option value="kuliah">Kuliah</option>
                                        <option value="kerja">Kerja</option>
                                        <option value="wirausaha">Wirausaha</option>
                                        <option value="belum_diketahui">Belum Diketahui</option>
                                    </select>
                                    @error('status_alumni') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Institusi --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Institusi {{ in_array($status_alumni, ['kuliah', 'kerja', 'wirausaha']) ? '*' : '(Opsional)' }}
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="institusi" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                        placeholder="Nama kampus/perusahaan/usaha"
                                    >
                                    @error('institusi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Bidang --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang (Opsional)</label>
                                    <input 
                                        type="text" 
                                        wire:model="bidang" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]"
                                        placeholder="Contoh: Teknik Informatika, Web Developer"
                                    >
                                    @error('bidang') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Status Publikasi --}}
                                <div>
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
                            </div>

                            {{-- Right Column --}}
                            <div class="space-y-4">
                                {{-- Foto Profile --}}
                                <div>
                                    <x-image-cropper 
                                        modelName="foto"
                                        label="Foto Alumni (1:1) - Opsional"
                                        aspectRatio="1"
                                        aspectRatioLabel="1:1"
                                        :maxWidth="800"
                                        :maxHeight="800"
                                        :currentImage="$currentFoto"
                                        :previewSize="200"
                                        :required="false"
                                    />
                                    @error('foto') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Deskripsi Markdown --}}
                                <div>
                                    <x-markdown-editor 
                                        modelName="deskripsi"
                                        label="Deskripsi Tambahan (Opsional)"
                                        placeholder="## Profil Singkat&#10;&#10;Ceritakan perjalanan alumni...&#10;&#10;**Pencapaian:**&#10;- Point 1&#10;- Point 2"
                                        :rows="12"
                                        :required="false"
                                    />
                                    @error('deskripsi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
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
    @if($showDetailModal && $detailAlumni)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75" wire:click="closeDetailModal"></div>

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
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nama</p>
                                <p class="text-lg font-semibold">{{ $detailAlumni->nama }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tahun Lulus</p>
                                <p class="text-lg font-semibold">{{ $detailAlumni->tahun_lulus }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Alumni</p>
                                <p class="text-lg font-semibold">{{ $detailAlumni->status_alumni_label }}</p>
                            </div>
                            @if($detailAlumni->institusi)
                                <div>
                                    <p class="text-sm text-gray-500">Institusi</p>
                                    <p class="text-lg font-semibold">{{ $detailAlumni->institusi }}</p>
                                </div>
                            @endif
                            @if($detailAlumni->bidang)
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Bidang</p>
                                    <p class="text-lg font-semibold">{{ $detailAlumni->bidang }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        @if($detailAlumni->deskripsi)
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Deskripsi</p>
                                <div class="prose max-w-none">
                                    {!! $detailAlumni->deskripsi_html !!}
                                </div>
                            </div>
                        @endif

                        {{-- Meta Info --}}
                        <div class="pt-4 border-t border-gray-200 text-sm text-gray-500">
                            <p>Dibuat oleh: {{ $detailAlumni->user->name }} pada {{ $detailAlumni->created_at->format('d M Y H:i') }}</p>
                            @if($detailAlumni->published_at)
                                <p>Dipublikasikan: {{ $detailAlumni->published_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
