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

    public function test(Request $request)
    {
        try {
            $discord = new DiscordService();
            $type = $request->input('type', 'register');

            // Get settings to test with current template
            $settings = Setting::all()->pluck('value', 'key');
            $lang = $settings['discord_active_language'] ?? 'en';

            $webhookUrl = null;
            $msg = '';

            if ($type === 'upload') {
                $webhookUrl = $settings['discord_webhook_upload'] ?? ($settings['discord_webhook_url'] ?? null);

                if (!$webhookUrl) {
                    return redirect()->back()->with('error', 'No Upload Webhook Configured.');
                }

                $templateKey = 'discord_template_upload_' . $lang;
                $msgTemplate = $settings[$templateKey] ?? '';

                if (empty($msgTemplate)) {
                    $msg = "📸 **Discord Upload Test**\n\n**TestImage** by **TestUser**\n\n(No template configured)";
                }
                else {
                    $msg = "📸 **Discord Upload Test** (using '{$lang}' template):\n\n" . str_replace(
                    ['{USER_NAME}', '{IMAGE_TITLE}', '{IMAGE_URL}'],
                    ['TestUser', 'TestImage', 'http://example.com'],
                        $msgTemplate
                    );
                }
            }
            else {
                // Register
                $webhookUrl = $settings['discord_webhook_register'] ?? ($settings['discord_webhook_url'] ?? null);

                if (!$webhookUrl) {
                    return redirect()->back()->with('error', 'No Registration Webhook Configured.');
                }

                $templateKey = 'discord_template_register_' . $lang;
                $msgTemplate = $settings[$templateKey] ?? '';

                if (empty($msgTemplate)) {
                    $msg = "🔔 **Discord Register Test**\n\n**TestUser** joined.\n(No template configured)";
                }
                else {
                    $msg = "🔔 **Discord Register Test** (using '{$lang}' template):\n\n" . str_replace('{USER_NAME}', 'TestUser', $msgTemplate);
                }
            }

            $discord->send($msg, null, $webhookUrl);
            return redirect()->back()->with('success', 'Test message sent to ' . ucfirst($type) . ' Discord channel!');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send Discord test: ' . $e->getMessage());
        }
    }
}
