@extends('admin.layouts.app')

@section('admin-content')
  <h2>Discord Integration</h2>

  <div class="panel">
    <p style="color:var(--muted); font-size:14px; margin-bottom:24px; max-width:600px; line-height:1.5;">
        Connect your SmartScope instance with a Discord server to receive real-time notifications about new members and uploads.
    </p>

    <form action="{{ route('admin.discord.update') }}" method="POST" style="max-width:600px;">
        @csrf
        
        <div class="card" style="background:rgba(88, 101, 242, 0.1); padding:20px; margin-bottom:24px; border:1px solid rgba(88, 101, 242, 0.2);">
            
            <div class="form-row" style="margin-bottom:20px;">
                <label style="display:flex; align-items:center; cursor:pointer; color:#fff; font-size:16px;">
                    <input type="checkbox" name="discord_enabled" value="1" {{ isset($settings['discord_enabled']) && $settings['discord_enabled'] ? 'checked' : '' }} style="width:20px; height:20px; margin-right:12px; accent-color:#5865F2;">
                    <strong>Enable Discord Integration</strong>
                </label>
            </div>
            
            <div class="form-row">
                <label>Webhook URL</label>
                <input type="text" name="discord_webhook_url" value="{{ $settings['discord_webhook_url'] ?? '' }}" placeholder="https://discord.com/api/webhooks/..." style="font-family:monospace;">
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    You can create a Webhook in your Discord Server Settings -> Integrations -> Webhooks.
                </div>
            </div>

            <div class="accent-line" style="margin:20px 0; background:rgba(255,255,255,0.1);"></div>

            <h4 style="margin-top:0; margin-bottom:16px;">Notification Events</h4>

            <div class="row-2">
                <div class="form-row">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="discord_notify_register" value="1" {{ isset($settings['discord_notify_register']) && $settings['discord_notify_register'] ? 'checked' : '' }} style="width:16px; height:16px; margin-right:8px; accent-color:#5865F2;">
                        Notify on New Registration
                    </label>
                </div>
                <div class="form-row">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="discord_notify_upload" value="1" {{ isset($settings['discord_notify_upload']) && $settings['discord_notify_upload'] ? 'checked' : '' }} style="width:16px; height:16px; margin-right:8px; accent-color:#5865F2;">
                        Notify on New Upload
                    </label>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:12px;">
            <button class="btn" type="submit" style="padding:10px 24px; font-size:14px;">Save Settings</button>
        </div>
    </form>

    <div style="margin-top:32px; padding-top:24px; border-top:1px solid var(--border);">
        <h4 style="margin-top:0;">Test Connection</h4>
        <p style="font-size:13px; color:var(--muted); margin-bottom:12px;">Ensure you have saved your settings before testing.</p>
        <form action="{{ route('admin.discord.test') }}" method="POST">
            @csrf
            <button class="btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.1);">Send Test Notification</button>
        </form>
    </div>

  </div>
@endsection
