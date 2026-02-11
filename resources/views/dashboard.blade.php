@extends('layouts.app')

@section('content')
<div class="row" style="display:flex; justify-content:center;">
    <div style="width:100%; max-width:1000px;">
        
        <!-- Welcome Section -->
        <div class="card" style="margin-bottom:32px; background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%); border:1px solid rgba(255,255,255,0.1);">
            <div style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
                <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; border:2px solid rgba(255,255,255,0.1); background:#000;">
                    <img src="{{ auth()->user()->avatar_url }}" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div style="flex:1;">
                    <h2 style="margin:0; font-size:24px; font-weight:600;">{{ __('messages.welcome_back') }}, {{ auth()->user()->display_name ?: auth()->user()->name }}!</h2>
                    <p style="margin:8px 0 0; color:var(--muted); font-size:14px;">{{ auth()->user()->email }}</p>
                </div>
                <div style="display:flex; gap:12px;">
                    <a href="{{ route('profile.edit') }}" class="btn" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.1); color:#fff;">
                        ✏️ {{ __('messages.edit_profile') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Actions Grid -->
        <h3 style="margin-bottom:16px; font-size:18px; color:var(--accent);">{{ __('messages.quick_actions') }}</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:24px; margin-bottom:48px;">
            
            <!-- My Collection -->
            <a href="{{ route('profile.edit') }}" class="card dashboard-card" style="text-decoration:none; color:inherit; transition:transform 0.2s, border-color 0.2s; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); height:100%;">
                <div style="display:flex; flex-direction:column; height:100%; padding:24px;">
                    <div style="font-size:32px; margin-bottom:16px;">📚</div>
                    <h4 style="margin:0 0 8px; font-size:18px;">{{ __('messages.my_collection') }}</h4>
                    <p style="margin:0; color:var(--muted); font-size:14px; flex:1;">{{ __('messages.view_collection_progress') }}</p>
                </div>
            </a>

            <!-- Moderation -->
            @if(auth()->user()->is_moderator || auth()->user()->is_admin)
            <a href="{{ route('admin.moderation.index') }}" class="card dashboard-card" style="text-decoration:none; color:inherit; transition:transform 0.2s, border-color 0.2s; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); height:100%;">
                <div style="display:flex; flex-direction:column; height:100%; padding:24px;">
                    <div style="font-size:32px; margin-bottom:16px;">🛡️</div>
                    <div style="font-weight:700; font-size:18px; margin-bottom:8px;">Moderation Queue</div>
                    <p style="margin:0; color:var(--muted); font-size:14px; flex:1;">Review reported content and uploaded images.</p>
                </div>
            </a>
            @endif

            <!-- Backups -->
            @if(auth()->user()->is_admin)
            <a href="{{ route('admin.backups.index') }}" class="card dashboard-card" style="text-decoration:none; color:inherit; transition:transform 0.2s, border-color 0.2s; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); height:100%;">
                <div style="display:flex; flex-direction:column; height:100%; padding:24px;">
                    <div style="font-size:32px; margin-bottom:16px;">💾</div>
                    <div style="font-weight:700; font-size:18px; margin-bottom:8px;">Database Backups</div>
                    <p style="margin:0; color:var(--muted); font-size:14px; flex:1;">Create and download database backups.</p>
                </div>
            </a>
            @endif

            <!-- Upload Image -->
            <a href="{{ route('images.create') }}" class="card dashboard-card" style="text-decoration:none; color:inherit; transition:transform 0.2s, border-color 0.2s; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); height:100%;">
                <div style="display:flex; flex-direction:column; height:100%; padding:24px;">
                    <div style="font-size:32px; margin-bottom:16px;">📤</div>
                    <h4 style="margin:0 0 8px; font-size:18px;">{{ __('messages.upload_image') }}</h4>
                    <p style="margin:0; color:var(--muted); font-size:14px; flex:1;">{{ __('messages.upload_submit') }}</p>
                </div>
            </a>

            <!-- Community -->
            @if(\App\Models\Setting::where('key', 'community_enabled')->value('value') !== '0')
            <a href="{{ route('community.index') }}" class="card dashboard-card" style="text-decoration:none; color:inherit; transition:transform 0.2s, border-color 0.2s; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); height:100%;">
                <div style="display:flex; flex-direction:column; height:100%; padding:24px;">
                    <div style="font-size:32px; margin-bottom:16px;">💬</div>
                    <h4 style="margin:0 0 8px; font-size:18px;">{{ __('messages.community') }}</h4>
                    <p style="margin:0; color:var(--muted); font-size:14px; flex:1;">{{ __('messages.community_desc') }}</p>
                </div>
            </a>
            @endif
        </div>

        <!-- Admin Section -->
        @if(auth()->user()->is_admin)
        <h3 style="margin-bottom:16px; font-size:18px; color:#ef4444;">{{ __('Administration') }}</h3>
        <div class="card" style="border:1px solid rgba(239, 68, 68, 0.2); background:rgba(239, 68, 68, 0.05); padding: 12px;">
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="{{ route('admin.users.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">👥 {{ __('Users') }}</a>
                <a href="{{ route('admin.moderation.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">🛡️ {{ __('Moderation') }}</a>
                <a href="{{ route('admin.images.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">🖼️ {{ __('Images') }}</a>
                <a href="{{ route('admin.objects.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">🔭 {{ __('Objects') }}</a>
                <a href="{{ route('admin.forum.categories.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">📂 {{ __('Forum') }}</a>
                <a href="{{ route('admin.news.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">📰 {{ __('News') }}</a>
                <a href="{{ route('admin.changelogs.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">📜 {{ __('Changelogs') }}</a>
                <a href="{{ route('admin.community.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">💬 {{ __('Community') }}</a>
                <a href="{{ route('admin.backups.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">💾 {{ __('Backups') }}</a>
                <a href="{{ route('admin.settings.index') }}" class="btn" style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); font-size:13px; color: #fff;">⚙️ {{ __('Settings') }}</a>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
    .dashboard-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent) !important;
        background: rgba(255,255,255,0.05) !important;
    }
</style>
@endsection
