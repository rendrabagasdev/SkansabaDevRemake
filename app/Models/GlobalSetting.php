<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GlobalSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_primary',
        'logo_secondary',
        'favicon',
        'primary_color',
        'secondary_color',
        'footer_text',
        'contact_email',
        'contact_phone',
        'whatsapp',
        'address',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'linkedin',
        'tiktok',
        'maps_url',
    ];

    /**
     * Get the singleton settings record.
     * Creates one if it doesn't exist.
     */
    public static function instance()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'site_name' => 'SMK RPL',
                'primary_color' => 'rgb(18,180,224)',
                'secondary_color' => 'rgb(255,255,255)',
            ]);
        }
        
        return $settings;
    }

    /**
     * Get URL for logo_primary
     */
    public function getLogoPrimaryUrlAttribute()
    {
        return $this->logo_primary ? Storage::url($this->logo_primary) : null;
    }

    /**
     * Get URL for logo_secondary
     */
    public function getLogoSecondaryUrlAttribute()
    {
        return $this->logo_secondary ? Storage::url($this->logo_secondary) : null;
    }

    /**
     * Get URL for favicon
     */
    public function getFaviconUrlAttribute()
    {
        return $this->favicon ? Storage::url($this->favicon) : null;
    }

    /**
     * Get primary color as CSS style
     */
    public function getPrimaryColorStyleAttribute()
    {
        return $this->primary_color ?: 'rgb(18,180,224)';
    }

    /**
     * Get secondary color as CSS style
     */
    public function getSecondaryColorStyleAttribute()
    {
        return $this->secondary_color ?: 'rgb(255,255,255)';
    }

    /**
     * Clear cache when settings are updated
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('global_settings');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('global_settings');
        });
    }
}
