@php
    $globalSettings = App\Models\GlobalSetting::instance();
@endphp
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

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Dynamic Primary Color CSS Variables --}}
    <style>
        :root {
            --primary-color: {{ $globalSettings->primary_color ?? '#12B4E0' }};
            --secondary-color: {{ $globalSettings->secondary_color ?? '#1E3A8A' }};
        }
    </style>
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="antialiased bg-gray-50">
    <x-navbar-guest />
    
    <main>
        {{ $slot }}
    </main>

    <livewire:components.footer />

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
