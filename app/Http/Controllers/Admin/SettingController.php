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
        if (!isset($data['ssl_enabled'])) {
            $data['ssl_enabled'] = '0';
        }
        if (!isset($data['enable_moderation'])) {
            $data['enable_moderation'] = '0';
        }
        if (!isset($data['community_enabled'])) {
            $data['community_enabled'] = '0';
        }
        if (!isset($data['forum_enabled'])) {
            $data['forum_enabled'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
            );
        }

        // Clear cache if you cache settings
        Cache::forget('app_settings');

        // Clear config cache to ensure new settings take effect immediately
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function syncObjects()
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'DeepSkyObjectsSeeder',
                '--force' => true // Required for production
            ]);
            return redirect()->back()->with('success', 'Deep Sky Objects synced successfully!');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync objects: ' . $e->getMessage());
        }
    }

    public function sendTestEmail(Request $request)
    {
        $user = auth()->user();

        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email from SmartScope.', function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('SMTP Test Email');
            });

            return redirect()->route('admin.settings.index')->with('success', 'Test email sent successfully to ' . $user->email);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.settings.index')->with('mail_error', $e->getMessage());
        }
    }
}
