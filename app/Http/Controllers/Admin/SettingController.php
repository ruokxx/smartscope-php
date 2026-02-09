<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        // Check checkbox for smtp_enabled (if unchecked, it won't be in request, handle that)
        if (!isset($data['smtp_enabled'])) {
            $data['smtp_enabled'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
            );
        }

        // Clear cache if you cache settings
        Cache::forget('app_settings');

        // Optional: clear config cache to ensure new settings take effect immediately if any were cached
        // Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
