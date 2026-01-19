<div 
    x-data="{ 
        collapsed: $persist(false).as('sidebar-collapsed'),
        mobileOpen: @entangle('isMobileOpen').live
    }" 
    @toggle-mobile-sidebar.window="mobileOpen = !mobileOpen"
    class="relative"
>
    {{-- Desktop Sidebar --}}
    <aside 
        class="hidden md:flex md:flex-shrink-0 transition-all duration-300"
        :class="collapsed ? 'md:w-20' : 'md:w-64'"
    >
        <div class="flex flex-col w-full bg-white border-r border-gray-200">
            {{-- Logo & Branding --}}
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <div class="flex items-center space-x-3 overflow-hidden">
                    @if($globalSettings->logo_primary_url)
                        <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="h-10 w-auto flex-shrink-0">
                    @else
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $globalSettings->primary_color_style }}">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    @endif
                    <div x-show="!collapsed" x-transition class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">{{ $globalSettings->site_name ?? 'CMS RPL' }}</div>
                        <div class="text-xs text-gray-500 truncate">Dashboard</div>
                    </div>
                </div>
                <button 
                    @click="collapsed = !collapsed"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition flex-shrink-0"
                    title="Toggle Sidebar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" x-show="!collapsed" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" x-show="collapsed" />
                    </svg>
                </button>
            </div>

            {{-- Navigation Menu --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @foreach($menuItems as $item)
                    @if($item['external'] ?? false)
                        {{-- External Link with Confirmation --}}
                        <a 
                            href="{{ $item['url'] }}"
                            target="_blank"
                            @click.prevent="if(confirm('Anda akan membuka link eksternal. Lanjutkan?')) window.open('{{ $item['url'] }}', '_blank')"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg group transition"
                            x-bind:class="collapsed ? 'justify-center' : ''"
                            x-bind:title="collapsed ? '{{ $item['label'] }}' : ''"
                        >
                            <x-dynamic-component :component="'icon-' . $item['icon']" class="w-5 h-5 flex-shrink-0" x-bind:class="collapsed ? '' : 'mr-3'" />
                            <span x-show="!collapsed" x-transition class="truncate">{{ $item['label'] }}</span>
                            <svg x-show="!collapsed" class="w-4 h-4 ml-auto flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @else
                        {{-- Internal Link --}}
                        <a 
                            href="{{ route($item['route']) }}"
                            wire:navigate
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition"
                            x-bind:class="collapsed ? 'justify-center' : ''"
                            x-bind:title="collapsed ? '{{ $item['label'] }}' : ''"
                            @class([
                                'bg-blue-50' => request()->routeIs($item['route'] . '*'),
                                'text-gray-700 hover:bg-gray-50' => !request()->routeIs($item['route'] . '*'),
                            ])
                            @if(request()->routeIs($item['route'] . '*'))
                                style="color: {{ $globalSettings->primary_color_style }}"
                            @endif
                        >
                            <x-dynamic-component :component="'icon-' . $item['icon']" class="w-5 h-5 flex-shrink-0" x-bind:class="collapsed ? '' : 'mr-3'" />
                            <span x-show="!collapsed" x-transition class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- User Profile --}}
            <div class="flex-shrink-0 px-3 py-4 border-t border-gray-200">
                <div class="flex items-center" :class="collapsed ? 'justify-center' : ''">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold" style="background-color: {{ $globalSettings->primary_color_style }}">
                            {{ auth()->user()->initials() }}
                        </div>
                    </div>
                    <div x-show="!collapsed" x-transition class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" x-show="!collapsed" x-transition>
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-gray-600" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile Sidebar Overlay --}}
    <div 
        x-show="mobileOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileOpen = false"
        class="fixed inset-0 bg-gray-600/75 z-40 md:hidden"
        style="display: none;"
    ></div>

    {{-- Mobile Sidebar --}}
    <div 
        x-show="mobileOpen"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white z-50 md:hidden"
        style="display: none;"
    >
        {{-- Mobile Header --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                @if($globalSettings->logo_primary_url)
                    <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="h-10 w-auto">
                @else
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center" style="background-color: {{ $globalSettings->primary_color_style }}">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="text-sm font-bold text-gray-900">{{ $globalSettings->site_name ?? 'CMS RPL' }}</div>
                    <div class="text-xs text-gray-500">Dashboard</div>
                </div>
            </div>
            <button @click="mobileOpen = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @foreach($menuItems as $item)
                @if($item['external'] ?? false)
                    <a 
                        href="{{ $item['url'] }}"
                        target="_blank"
                        @click.prevent="if(confirm('Anda akan membuka link eksternal. Lanjutkan?')) { window.open('{{ $item['url'] }}', '_blank'); mobileOpen = false; }"
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg"
                    >
                        <x-dynamic-component :component="'icon-' . $item['icon']" class="w-5 h-5 mr-3" />
                        <span>{{ $item['label'] }}</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @else
                    <a 
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        @click="mobileOpen = false"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
                        @class([
                            'bg-blue-50' => request()->routeIs($item['route'] . '*'),
                            'text-gray-700 hover:bg-gray-50' => !request()->routeIs($item['route'] . '*'),
                        ])
                        @if(request()->routeIs($item['route'] . '*'))
                            style="color: {{ $globalSettings->primary_color_style }}"
                        @endif
                    >
                        <x-dynamic-component :component="'icon-' . $item['icon']" class="w-5 h-5 mr-3" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Mobile User Profile --}}
        <div class="flex-shrink-0 px-3 py-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold" style="background-color: {{ $globalSettings->primary_color_style }}">
                        {{ auth()->user()->initials() }}
                    </div>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->role }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
