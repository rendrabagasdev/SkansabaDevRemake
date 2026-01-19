@props([
    'modelName' => 'image',
    'cropDataName' => 'crop_data',
    'label' => 'Pilih Gambar',
    'aspectRatio' => 1,
    'aspectRatioLabel' => '1:1',
    'maxWidth' => 1200,
    'maxHeight' => 1200,
    'required' => false,
    'currentImage' => null,
    'previewSize' => 200
])

<div x-data="{
    croppie: null,
    showCropper: false,
    fileName: '',
    originalFile: null,
    cropResult: null,
    
    init() {
        window.imageCropper = this;
    },
    
    initCroppie() {
        console.log('initCroppie called');
        
        if (typeof Croppie === 'undefined') {
            console.error('Croppie not loaded!');
            return;
        }
        
        const el = this.$refs.croppieContainer;
        if (this.croppie) {
            this.croppie.destroy();
        }
        
        const viewportWidth = {{ $previewSize }};
        const viewportHeight = Math.round(viewportWidth / {{ $aspectRatio }});
        const boundaryWidth = Math.max(viewportWidth + 100, 400);
        const boundaryHeight = Math.max(viewportHeight + 100, 400);
        
        this.croppie = new Croppie(el, {
            viewport: { width: viewportWidth, height: viewportHeight, type: 'square' },
            boundary: { width: boundaryWidth, height: boundaryHeight },
            showZoomer: true,
            enableOrientation: true,
            enableResize: false,
            mouseWheelZoom: 'ctrl',
            enforceBoundary: true
        });
        
        // Real-time update saat user drag/zoom
        el.addEventListener('update', () => {
            this.updateCrop();
        });
        
        console.log('Croppie ready');
    },
    
    handleFileChange(event) {
        const file = event.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        
        const fileName = file.name.toLowerCase();
        if (fileName.endsWith('.heic') || fileName.endsWith('.heif')) {
            alert('Format HEIC tidak didukung. Gunakan JPG/PNG/WebP');
            event.target.value = '';
            return;
        }
        
        this.fileName = file.name;
        this.originalFile = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            this.showCropper = true;
            this.$nextTick(() => {
                this.initCroppie();
                setTimeout(() => {
                    // Bind dengan zoom 0 untuk minimum zoom (full image)
                    this.croppie.bind({
                        url: e.target.result,
                        zoom: 0,  // Minimum zoom untuk show full image
                        orientation: 1
                    }).then(() => {
                        console.log('Image bound with minimum zoom');
                        this.updateCrop();
                    });
                }, 100);
            });
        };
        reader.readAsDataURL(file);
    },
    
    updateCrop() {
        if (!this.croppie) return;
        
        this.croppie.result({
            type: 'base64',
            size: { width: {{ $maxWidth }}, height: {{ $maxHeight }} },
            format: 'png',  // Use PNG to preserve transparency
            quality: 1
        }).then((result) => {
            this.cropResult = result;
            // Store ONLY in Alpine, do NOT send to Livewire yet!
        });
    },
    
    sendToLivewire() {
        // Called manually on form submit
        if (this.cropResult) {
            this.$wire.{{ $modelName }} = this.cropResult;
        }
    },
    
    rotate(degrees) {
        if (this.croppie) {
            this.croppie.rotate(degrees);
            setTimeout(() => this.updateCrop(), 100);
        }
    },
    
    clearImage() {
        if (this.croppie) this.croppie.destroy();
        this.croppie = null;
        this.showCropper = false;
        this.cropResult = null;
        this.$refs.fileInput.value = '';
        this.$wire.set('{{ $modelName }}', null);
        this.$wire.set('{{ $cropDataName }}', null);
    }
}" @croppie-update.window="updateCrop()">
    <div class="space-y-4">
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>

        <div class="flex items-center gap-2 text-xs text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Rasio <strong>{{ $aspectRatioLabel }}</strong>. Format: JPG, PNG, WebP.</span>
        </div>

        @if($currentImage)
            <div x-show="!showCropper" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-600 mb-2">Gambar Saat Ini:</p>
                <img src="{{ asset('storage/' . $currentImage) }}" alt="Current" class="w-full max-w-md h-auto object-cover rounded-lg border-2 border-gray-300">
            </div>
        @endif

        <input 
            type="file" 
            x-ref="fileInput"
            @change="handleFileChange($event)"
            accept="image/jpeg,image/jpg,image/png,image/webp"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#12B4E0] file:text-white hover:file:bg-[#0e91b8] cursor-pointer"
        >

        @error($modelName) <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <div x-show="showCropper" x-cloak class="space-y-4">
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex gap-2">
                        <button type="button" @click="rotate(-90)" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">← Rotate</button>
                        <button type="button" @click="rotate(90)" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">Rotate →</button>
                    </div>
                    <button type="button" @click="clearImage()" class="px-3 py-2 bg-red-50 border border-red-300 text-red-700 rounded-lg hover:bg-red-100 text-sm">Hapus</button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div x-ref="croppieContainer" class="bg-gray-100 rounded-lg border-2 border-gray-300"></div>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 mb-2">Preview:</p>
                        <div class="crop-preview-box bg-gray-100">
                            <img x-show="cropResult" :src="cropResult" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="text-xs text-gray-600 space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <p><strong>File:</strong> <span x-text="fileName" class="font-mono"></span></p>
                        <p><strong>Rasio:</strong> {{ $aspectRatioLabel }}</p>
                        <p><strong>Output:</strong> {{ $maxWidth }}x{{ $maxHeight }}px</p>
                        <p class="text-green-600">✓ Auto: Crop, WebP, Compress</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
