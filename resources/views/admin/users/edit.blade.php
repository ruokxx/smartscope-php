@extends('admin.layouts.app')

@section('admin-content')
  <h2>Edit user #{{ $user->id }}</h2>

  <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="form-row">
      <label>Name</label>
      <input name="name" value="{{ old('name', $user->name) }}">
    </div>

    <div class="form-row">
      <label>Email</label>
      <input name="email" value="{{ old('email', $user->email) }}">
    </div>

<div class="form-row">
  <input type="hidden" name="is_admin" value="0">
  <label><input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}> Is admin</label>
</div>

    <div style="margin-top:12px">
      <button class="btn" type="submit">Save</button>
      <a href="{{ route('admin.users.index') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Cancel</a>
    </div>
  </form>

<div class="accent-line"></div>

<div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:16px;">
    <h3 style="margin-top:0; font-size:16px;">Email Verification</h3>
    <div style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            Status: 
            @if($user->hasVerifiedEmail()) 
                <span style="color:#2ecc71; font-weight:bold;">Verified</span>
                <div style="font-size:12px; color:var(--muted);">{{ $user->email_verified_at->format('Y-m-d H:i') }}</div>
            @else 
                <span style="color:#e74c3c; font-weight:bold;">Unverified</span>
            @endif
        </div>
        <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
            @csrf
            @if($user->hasVerifiedEmail())
                <button class="btn" onclick="return confirm('Mark email as unverified?')" style="background:rgba(255,255,255,0.1); color:#fff; font-size:12px;">Unverify</button>
            @else
                <button class="btn" onclick="return confirm('Manually mark email as verified?')" style="background:var(--success); color:#000; font-size:12px;">Mark Verified</button>
            @endif
        </form>
    </div>
</div>

<div class="card" style="background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1); padding:16px;">
    <h3 style="margin-top:0; font-size:16px; color:#fca5a5;">Ban Management</h3>
    
    @if($user->banned_at)
        <div style="margin-bottom:12px;">
            <div style="color:#fca5a5; font-weight:bold; margin-bottom:4px;">User is BANNED</div>
            <div style="font-size:13px; color:#fca5a5;">
                @if($user->banned_until)
                    Until: {{ $user->banned_until->format('Y-m-d H:i') }} ({{ $user->banned_until->diffForHumans() }})
                @else
                    PERMANENTLY
                @endif
            </div>
        </div>
        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST">
            @csrf
            <button class="btn" style="background:var(--success); color:#000; width:100%;">Unban User</button>
        </form>
    @else
        <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
            @csrf
            <div class="form-row">
                <label style="color:#fca5a5;">Ban Duration</label>
                <div class="styled-select-container">
                    <select name="duration" class="styled-select">
                        <option value="permanent">Permanent</option>
                        <option value="24h">24 Hours</option>
                        <option value="3d">3 Days</option>
                        <option value="1w">1 Week</option>
                        <option value="1m">1 Month</option>
                    </select>
                </div>
            </div>
            <button class="btn" style="background:var(--danger); color:#fff; width:100%;" onclick="return confirm('Ban this user?')">Ban User</button>
        </form>
    @endif
</div>
@endsection
