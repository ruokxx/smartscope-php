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
            
            <div class="row-2">
                <div class="form-row">
                    <label>Registration Webhook URL</label>
                    <input type="text" name="discord_webhook_register" value="{{ $settings['discord_webhook_register'] ?? ($settings['discord_webhook_url'] ?? '') }}" placeholder="https://discord.com/api/webhooks/..." style="font-family:monospace;">
                </div>
                <div class="form-row">
                    <label>Upload Webhook URL</label>
                    <input type="text" name="discord_webhook_upload" value="{{ $settings['discord_webhook_upload'] ?? ($settings['discord_webhook_url'] ?? '') }}" placeholder="https://discord.com/api/webhooks/..." style="font-family:monospace;">
                </div>
            </div>
            <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                You can create Webhooks in Discord Server Settings -> Integrations -> Webhooks. You can use different URLs for different channels.
            </div>

            <div class="form-row" style="margin-top:16px;">
                <label>Invite Link (Footer)</label>
                <input type="text" name="discord_invite_url" value="{{ $settings['discord_invite_url'] ?? '' }}" placeholder="https://discord.gg/...">
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    This link will be displayed in the site footer as a Discord icon.
                </div>
            </div>

            <div class="form-row" style="margin-top:16px;">
                <label>Active Language</label>
                <select name="discord_active_language" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px; border-radius:4px; width:100%;">
                    <option value="en" {{ (isset($settings['discord_active_language']) && $settings['discord_active_language'] == 'en') ? 'selected' : '' }}>English</option>
                    <option value="de" {{ (isset($settings['discord_active_language']) && $settings['discord_active_language'] == 'de') ? 'selected' : '' }}>German</option>
                </select>
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    Select which language template to use for notifications.
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

            <div class="accent-line" style="margin:20px 0; background:rgba(255,255,255,0.1);"></div>

            <h4 style="margin-top:0; margin-bottom:16px;">Message Templates</h4>

            <!-- Registration Templates -->
            <div style="margin-bottom:24px;">
                <h5 style="margin-bottom:8px; color:#aec6cf;">Registration Notification</h5>
                <div class="form-row">
                    <label>English Template</label>
                    <textarea name="discord_template_register_en" rows="3" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px; border-radius:4px; font-family:monospace;">{{ $settings['discord_template_register_en'] ?? "🎉 **New Member Joined!**\n\nPassionate Astrophotographer **{USER_NAME}** has just joined SmartScope! 🔭" }}</textarea>
                </div>
                <div class="form-row" style="margin-top:8px;">
                    <label>German Template</label>
                    <textarea name="discord_template_register_de" rows="3" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px; border-radius:4px; font-family:monospace;">{{ $settings['discord_template_register_de'] ?? "🎉 **Neues Mitglied!**\n\nPassonierter Astrofotograf **{USER_NAME}** ist SmartScope beigetreten! 🔭" }}</textarea>
                </div>
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    Available placeholders: <code>{USER_NAME}</code>
                </div>
            </div>

            <!-- Upload Templates -->
            <div>
                <h5 style="margin-bottom:8px; color:#aec6cf;">New Upload Notification</h5>
                <div class="form-row">
                    <label>English Template</label>
                    <textarea name="discord_template_upload_en" rows="3" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px; border-radius:4px; font-family:monospace;">{{ $settings['discord_template_upload_en'] ?? "📸 **New Image Uploaded!**\n\n**{IMAGE_TITLE}** by **{USER_NAME}**\n\n{IMAGE_URL}" }}</textarea>
                </div>
                <div class="form-row" style="margin-top:8px;">
                    <label>German Template</label>
                    <textarea name="discord_template_upload_de" rows="3" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px; border-radius:4px; font-family:monospace;">{{ $settings['discord_template_upload_de'] ?? "📸 **Neues Bild hochgeladen!**\n\n**{IMAGE_TITLE}** von **{USER_NAME}**\n\n{IMAGE_URL}" }}</textarea>
                </div>
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    Available placeholders: <code>{USER_NAME}</code>, <code>{IMAGE_TITLE}</code>, <code>{IMAGE_URL}</code>
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
            <button name="type" value="register" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.1); margin-right:8px;">Test Registration Hook</button>
            <button name="type" value="upload" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.1);">Test Upload Hook</button>
        </form>
    </div>

  </div>
@endsection
