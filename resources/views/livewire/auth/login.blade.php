<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        {{-- Login Card --}}
        <div class="bg-white rounded-xl shadow-lg p-8">
            {{-- Logo & Branding --}}
            <div class="text-center mb-8">
                @if($globalSettings->logo_primary_url)
                    <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="mx-auto h-16 w-auto mb-4">
                @else
                    <div class="mx-auto h-16 w-16 rounded-lg flex items-center justify-center mb-4" style="background-color: {{ $globalSettings->primary_color_style }}">
                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-gray-900">{{ $globalSettings->site_name ?? 'CMS Jurusan RPL' }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $globalSettings->site_tagline ?? 'Sekolah Menengah Kejuruan' }}</p>
            </div>

            {{-- Login Form --}}
            <form wire:submit.prevent="login" class="space-y-6">
                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model.live.debounce.300ms="email"
                        @disabled($isLoading)
                        class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-500"
                        style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        autofocus
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="{{ $showPassword ? 'text' : 'password' }}" 
                            id="password" 
                            wire:model.live.debounce.300ms="password"
                            @disabled($isLoading)
                            class="appearance-none block w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-500"
                            style="--tw-ring-color: {{ $globalSettings->primary_color_style }}"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            wire:click="togglePassword"
                            @disabled($isLoading)
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition disabled:hover:text-gray-400"
                            tabindex="-1"
                        >
                            @if($showPassword)
                                {{-- Eye Slash Icon --}}
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            @else
                                {{-- Eye Icon --}}
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Forgot Password Link (Optional) --}}
                <div class="flex items-center justify-end">
                    <a href="#" class="text-sm font-medium hover:underline transition" style="color: {{ $globalSettings->primary_color_style }}">
                        Lupa password?
                    </a>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit" 
                    @disabled($isLoading)
                    class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color: {{ $globalSettings->primary_color_style }}; --tw-ring-color: {{ $globalSettings->primary_color_style }}"
                >
                    @if($isLoading)
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    @else
                        Masuk
                    @endif
                </button>
            </form>

            {{-- Footer --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} Jurusan RPL. Hanya untuk staff internal.
                </p>
            </div>
        </div>
    </div>
</div>
