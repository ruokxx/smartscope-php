<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class DiscordController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.discord.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        // Checkboxes handling
        if (!isset($data['discord_enabled']))
            $data['discord_enabled'] = '0';
        if (!isset($data['discord_notify_register']))
            $data['discord_notify_register'] = '0';
        if (!isset($data['discord_notify_upload']))
            $data['discord_notify_upload'] = '0';

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
            );
        }

        // Clear cache
        Cache::forget('app_settings');
        Artisan::call('config:clear');

        return redirect()->route('admin.discord.index')->with('success', 'Discord settings updated successfully.');
    }

    public function test()
    {
        try {
            $discord = new DiscordService();
            $discord->send("🔔 **Discord Integration Test**\n\nIf you can read this, the SmartScope Webhook integration is working successfully! 🚀");
            return redirect()->back()->with('success', 'Test message sent to Discord!');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send Discord test: ' . $e->getMessage());
        }
    }
}
