<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? $globalSettings->site_name ?? 'CMS Jurusan RPL' }}</title>
    
    {{-- Favicon --}}
    @if($globalSettings->favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $globalSettings->favicon_url }}">
    @endif
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $globalSettings->site_tagline ?? 'Sistem Manajemen Konten Jurusan RPL' }}">
    <meta name="author" content="{{ $globalSettings->site_name ?? 'SMK RPL' }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    {{-- Croppie CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-zxBiDORGDEAYDdKLuYU9X/JaJo/DPzE42UubfBw9yg8Qvb2YRRIQ8v4KsGHOx2H1/+sdSXyXxLXv5r7tHc9ygg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Dynamic Primary Color CSS Variables --}}
    <style>
        :root {
            --primary-color: {{ $globalSettings->primary_color_style }};
            --secondary-color: {{ $globalSettings->secondary_color_style }};
        }
    </style>
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="antialiased bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar Component --}}
        <livewire:components.sidebar-menu />

        {{-- Main Content --}}
        <div class="flex flex-col flex-1 overflow-hidden">
            {{-- Mobile Header --}}
            <header class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 md:hidden">
                <div class="flex items-center space-x-3">
                    @if($globalSettings->logo_primary_url)
                        <img src="{{ $globalSettings->logo_primary_url }}" alt="{{ $globalSettings->site_name }}" class="h-8 w-auto">
                    @else
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background-color: {{ $globalSettings->primary_color_style }}">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-sm font-bold text-gray-900">{{ $globalSettings->site_name ?? 'CMS RPL' }}</span>
                </div>
                <button wire:click="$dispatch('toggle-mobile-sidebar')" class="text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
