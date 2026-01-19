<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Prestasi Siswa</h1>
                <p class="text-gray-600 mt-1">Manajemen data prestasi siswa RPL</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-6 py-3 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold flex items-center gap-2 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Prestasi
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
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Cari judul, nama siswa, kelas..."
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
                    <select wire:model.live="filterJenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Jenis</option>
                        <option value="akademik">Akademik</option>
                        <option value="non-akademik">Non-Akademik</option>
                        <option value="kompetisi">Kompetisi</option>
                        <option value="sertifikasi">Sertifikasi</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterTingkat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0]">
                        <option value="">Semua Tingkat</option>
                        <option value="sekolah">Sekolah</option>
                        <option value="kecamatan">Kecamatan</option>
                        <option value="kota">Kota</option>
                        <option value="provinsi">Provinsi</option>
                        <option value="nasional">Nasional</option>
                        <option value="internasional">Internasional</option>
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tingkat</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('tahun')">
                                Tahun
                                @if($sortField === 'tahun')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($prestasis as $prestasi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($prestasi->gambar)
                                            <img src="{{ asset('storage/' . $prestasi->gambar) }}" alt="{{ $prestasi->judul }}" class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $prestasi->judul }}</div>
                                            <div class="text-sm text-gray-500">{{ $prestasi->penyelenggara }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $prestasi->nama_siswa }}</div>
                                        <div class="text-gray-500">{{ $prestasi->kelas }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $prestasi->jenis === 'akademik' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $prestasi->jenis === 'non-akademik' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $prestasi->jenis === 'kompetisi' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $prestasi->jenis === 'sertifikasi' ? 'bg-orange-100 text-orange-800' : '' }}
                                    ">
                                        {{ ucfirst($prestasi->jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        {{ in_array($prestasi->tingkat, ['nasional', 'internasional']) ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}
                                    ">
                                        {{ ucfirst($prestasi->tingkat) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $prestasi->tahun }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $prestasi->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $prestasi->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $prestasi->status === 'review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $prestasi->status === 'archived' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($prestasi->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if(!$prestasi->trashed())
                                            <button 
                                                wire:click="openEditModal({{ $prestasi->id }})"
                                                class="text-blue-600 hover:text-blue-800"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button 
                                                wire:click="delete({{ $prestasi->id }})"
                                                wire:confirm="Yakin ingin menghapus prestasi ini?"
                                                class="text-red-600 hover:text-red-800"
                                                title="Hapus"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button 
                                                wire:click="restore({{ $prestasi->id }})"
                                                class="text-green-600 hover:text-green-800"
                                                title="Restore"
                                            >
                                                Restore
                                            </button>
                                            <button 
                                                wire:click="forceDelete({{ $prestasi->id }})"
                                                wire:confirm="Yakin ingin menghapus permanen?"
                                                class="text-red-600 hover:text-red-800"
                                                title="Hapus Permanen"
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
                                    <p class="text-lg font-semibold">Tidak ada data prestasi</p>
                                    <p class="text-sm mt-1">Klik tombol "Tambah Prestasi" untuk membuat data baru</p>
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
                                {{ $isEditMode ? 'Edit Prestasi' : 'Tambah Prestasi Baru' }}
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
                                id="prestasiForm"
                            >
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {{-- Left Column --}}
                                    <div class="space-y-6">
                                        {{-- Judul --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Judul Prestasi <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="judul"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                placeholder="Contoh: Juara 1 Lomba Web Design"
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

                                        {{-- Jenis & Tingkat --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Jenis <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    wire:model="jenis"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                    <option value="akademik">Akademik</option>
                                                    <option value="non-akademik">Non-Akademik</option>
                                                    <option value="kompetisi">Kompetisi</option>
                                                    <option value="sertifikasi">Sertifikasi</option>
                                                </select>
                                                @error('jenis') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tingkat <span class="text-red-500">*</span>
                                                </label>
                                                <select 
                                                    wire:model="tingkat"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                    <option value="sekolah">Sekolah</option>
                                                    <option value="kecamatan">Kecamatan</option>
                                                    <option value="kota">Kota</option>
                                                    <option value="provinsi">Provinsi</option>
                                                    <option value="nasional">Nasional</option>
                                                    <option value="internasional">Internasional</option>
                                                </select>
                                                @error('tingkat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        {{-- Penyelenggara --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Penyelenggara <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="penyelenggara"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                placeholder="Contoh: Dinas Pendidikan Provinsi"
                                            >
                                            @error('penyelenggara') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Tanggal & Tahun --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Tanggal Prestasi <span class="text-red-500">*</span>
                                                </label>
                                                <input 
                                                    type="date" 
                                                    wire:model="tanggal_prestasi"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent"
                                                >
                                                @error('tanggal_prestasi') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                            </div>
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
                                        </div>

                                        {{-- Status --}}
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

                                        {{-- Gambar --}}
                                        <div>
                                            <x-image-cropper
                                                modelName="gambar"
                                                label="Gambar Prestasi"
                                                :aspectRatio="16/9"
                                                aspectRatioLabel="16:9"
                                                maxWidth="1920"
                                                maxHeight="1080"
                                                :currentImage="$currentGambar"
                                                previewSize="200"
                                            />
                                        </div>

                                        {{-- Sertifikat --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Sertifikat (PDF/Gambar)
                                            </label>
                                            @if($currentSertifikat)
                                                <div class="mb-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <p class="text-sm text-gray-600">File saat ini: <strong>{{ basename($currentSertifikat) }}</strong></p>
                                                </div>
                                            @endif
                                            <input 
                                                type="file" 
                                                wire:model="sertifikat"
                                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer"
                                            >
                                            @error('sertifikat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    {{-- Right Column: Deskripsi dengan Markdown Editor --}}
                                    <div>
                                        <x-markdown-editor 
                                            modelName="deskripsi"
                                            label="Deskripsi"
                                            placeholder="## Deskripsi Prestasi&#10;&#10;Tuliskan detail lengkap tentang prestasi yang diraih.&#10;&#10;**Gunakan format Markdown:**&#10;- **Bold** untuk teks penting&#10;- *Italic* untuk penekanan&#10;- ## Heading untuk judul bagian"
                                            :rows="20"
                                            :required="true"
                                        />
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                                    <button 
                                        type="button"
                                        @click="$wire.closeModal()"
                                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold transition"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        class="px-6 py-2 bg-[#12B4E0] text-white rounded-lg hover:bg-[#0e91b8] font-semibold transition disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="save">
                                            {{ $isEditMode ? 'Update' : 'Simpan' }}
                                        </span>
                                        <span wire:loading wire:target="save">
                                            Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
