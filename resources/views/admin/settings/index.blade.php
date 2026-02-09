@extends('layouts.app')

@section('content')
<div class="card full">
    <h2>SMTP Configuration</h2>
    
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="upload-form">
        @csrf
        
        <div class="form-row">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="smtp_enabled" value="1" {{ isset($settings['smtp_enabled']) && $settings['smtp_enabled'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                Enable Registration Emails
            </label>
        </div>

        <div class="accent-line"></div>
        <h3>Server Settings</h3>

        <div class="row-2">
            <div class="form-row">
                <label>Mail Host</label>
                <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.example.com">
            </div>
            <div class="form-row">
                <label>Mail Port</label>
                <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}">
            </div>
        </div>

        <div class="row-2">
            <div class="form-row">
                <label>Mail Username</label>
                <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}">
            </div>
            <div class="form-row">
                <label>Mail Password</label>
                <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}">
            </div>
        </div>

        <div class="form-row">
            <label>Encryption</label>
            <div class="styled-select-container">
                <select name="mail_encryption" class="styled-select">
                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="accent-line"></div>
        <h3>Sender Settings</h3>

        <div class="row-2">
            <div class="form-row">
                <label>From Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="noreply@example.com">
            </div>
            <div class="form-row">
                <label>From Name</label>
                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('app.name') }}">
            </div>
        </div>

        <button type="submit" class="btn" style="margin-top:12px;">Save Settings</button>
    </form>
</div>
@endsection
