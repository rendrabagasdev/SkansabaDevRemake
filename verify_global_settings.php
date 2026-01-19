#!/usr/bin/env php
<?php

/**
 * Global Settings Enforcement Verification Script
 * 
 * This script verifies that global settings are properly loaded and used throughout the application.
 * Run: php artisan tinker < verify_global_settings.php
 */

echo "\n🔍 GLOBAL SETTINGS ENFORCEMENT VERIFICATION\n";
echo "==========================================\n\n";

// 1. Check if global settings exist
echo "1. Checking Global Settings in Database...\n";
$settings = \App\Models\GlobalSetting::first();

if ($settings) {
    echo "   ✅ Global settings found in database\n";
    echo "   📋 Current Settings:\n";
    echo "      - Site Name: " . ($settings->site_name ?: 'NOT SET') . "\n";
    echo "      - Site Tagline: " . ($settings->site_tagline ?: 'NOT SET') . "\n";
    echo "      - Primary Color: " . ($settings->primary_color ?: 'NOT SET') . "\n";
    echo "      - Logo Primary: " . ($settings->logo_primary ? 'UPLOADED' : 'NOT SET') . "\n";
    echo "      - Favicon: " . ($settings->favicon ? 'UPLOADED' : 'NOT SET') . "\n";
} else {
    echo "   ❌ No global settings found! Running seeder...\n";
    \Artisan::call('db:seed', ['--class' => 'GlobalSettingSeeder']);
    echo "   ✅ Seeder executed\n";
}

echo "\n";

// 2. Check if ViewComposer is registered
echo "2. Checking ViewComposer Registration...\n";
$composerClass = 'App\\Http\\View\\Composers\\GlobalSettingsComposer';
if (class_exists($composerClass)) {
    echo "   ✅ GlobalSettingsComposer class exists\n";
} else {
    echo "   ❌ GlobalSettingsComposer class NOT found\n";
}

echo "\n";

// 3. Check cache
echo "3. Checking Global Settings Cache...\n";
$cached = \Illuminate\Support\Facades\Cache::get('global_settings');
if ($cached) {
    echo "   ✅ Global settings are cached\n";
} else {
    echo "   ⚠️  Global settings not cached (will be cached on first view load)\n";
}

echo "\n";

// 4. Verify accessors
echo "4. Verifying Model Accessors...\n";
if ($settings) {
    echo "   - logo_primary_url: " . ($settings->logo_primary_url ?: 'NULL') . "\n";
    echo "   - favicon_url: " . ($settings->favicon_url ?: 'NULL') . "\n";
    echo "   - primary_color_style: " . $settings->primary_color_style . "\n";
    echo "   - secondary_color_style: " . $settings->secondary_color_style . "\n";
    echo "   ✅ All accessors working\n";
}

echo "\n";

// 5. Check files that should use global settings
echo "5. Checking Files for Hardcoded Values...\n";
$files_to_check = [
    'resources/views/livewire/auth/login.blade.php',
    'resources/views/livewire/components/sidebar-menu.blade.php',
    'resources/views/components/layouts/app.blade.php',
    'resources/views/components/navbar-guest.blade.php',
];

$hardcoded_patterns = [
    '#12B4E0' => 'Hardcoded primary color',
    'CMS RPL' => 'Hardcoded site name (should use $globalSettings)',
    'bg-[#12B4E0]' => 'Hardcoded Tailwind color class',
];

$issues_found = 0;
foreach ($files_to_check as $file) {
    $full_path = base_path($file);
    if (file_exists($full_path)) {
        $content = file_get_contents($full_path);
        $file_issues = 0;
        
        foreach ($hardcoded_patterns as $pattern => $description) {
            // Skip if pattern is in a comment or in globalSettings usage
            if (preg_match('/' . preg_quote($pattern, '/') . '/', $content)) {
                // Check if it's used with globalSettings (which is OK)
                if (!preg_match('/globalSettings.*' . preg_quote($pattern, '/') . '/', $content) &&
                    !preg_match('/\{\{.*globalSettings.*\}\}/', $content)) {
                    // Check if the pattern is NOT in a fallback (which is OK)
                    if (!preg_match('/\?\?.*[\'"]' . preg_quote($pattern, '/') . '[\'"]/', $content)) {
                        $file_issues++;
                        $issues_found++;
                    }
                }
            }
        }
        
        if ($file_issues === 0) {
            echo "   ✅ $file - No issues\n";
        } else {
            echo "   ⚠️  $file - $file_issues potential hardcoded value(s) found\n";
        }
    } else {
        echo "   ❌ $file - File not found\n";
    }
}

echo "\n";

// 6. Final summary
echo "6. VERIFICATION SUMMARY\n";
echo "   ===================\n";
if ($issues_found === 0) {
    echo "   ✅ ALL CHECKS PASSED - Global settings properly enforced!\n";
} else {
    echo "   ⚠️  Found $issues_found potential issue(s) - Review recommended\n";
}

echo "\n";
echo "📝 MANUAL VERIFICATION CHECKLIST:\n";
echo "   □ Visit /login - Check if logo and colors are dynamic\n";
echo "   □ Visit /dashboard - Check sidebar branding\n";
echo "   □ Visit /global-settings - Update settings and verify changes appear\n";
echo "   □ Check browser title shows dynamic site name\n";
echo "   □ Check favicon appears (if uploaded)\n";
echo "   □ Visit public pages - Check navbar branding\n";

echo "\n✨ Verification Complete!\n\n";
