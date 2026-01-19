<?php

namespace App\Http\View\Composers;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class GlobalSettingsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cache global settings for 1 hour to reduce database queries
        $settings = Cache::remember('global_settings', 3600, function () {
            return GlobalSetting::instance();
        });

        $view->with('globalSettings', $settings);
    }
}
