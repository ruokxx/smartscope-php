@extends('layouts.app')

@section('content')
<style>
    .profile-layout {
        display: flex;
        flex-wrap: wrap;
        align-items: start;
        gap: 32px;
    }
    .profile-col-left {
        flex: 0 0 300px;
        min-width: 300px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .profile-col-center {
        flex: 1;
        min-width: 0; /* allows grid to shrink */
    }
    .profile-col-right {
        flex: 0 0 260px;
        min-width: 260px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    @media (max-width: 900px) {
        .profile-layout {
            flex-direction: column;
            gap: 24px;
        }
        .profile-col-left, 
        .profile-col-center, 
        .profile-col-right {
            flex: 1;
            width: 100%;
            min-width: auto;
        }
    }
    @keyframes blink {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.2); color: #ffeb3b; }
        100% { opacity: 1; transform: scale(1); }
    }
    .blink-icon {
        animation: blink 1.5s infinite;
        display: inline-block;
    }
</style>

<div class="profile-layout">
      <!-- Left Column: Profile Settings -->
      <div class="profile-col-left">
          <!-- Profile Settings Card -->
          <div class="card" style="padding:0; overflow:hidden; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
              <div style="background: linear-gradient(90deg, rgba(111,184,255,0.1), rgba(178,123,255,0.1)); padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0; font-size:18px; font-weight:600; color:#fff;">{{ __('Profile') }}</h2>
                <div style="display:flex; gap:16px; align-items:center;">
                    <a href="{{ route('dashboard') }}" style="font-size:12px; color:var(--muted); text-decoration:none; display:flex; align-items:center; gap:6px;">
                        ⬅️ {{ __('Dashboard') }}
                    </a>
                <a href="{{ route('messages.index') }}" style="font-size:12px; color:var(--accent); text-decoration:none; display:flex; align-items:center; gap:6px;">
                    <span class="{{ isset($unreadCount) && $unreadCount > 0 ? 'blink-icon' : '' }}">✉️</span> 
                    {{ __('Inbox') }}
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span style="background:var(--accent); color:#fff; border-radius:50%; padding:2px 6px; font-size:10px; font-weight:bold;">{{ $unreadCount }}</span>
                    @endif
                </a>
              </div>
            </div>

              <div style="padding:24px;">
                  
                  <!-- Avatar & Progress Display -->
                  <div style="text-align:center; margin-bottom:24px;">
                      <div style="width:100px; height:100px; border-radius:50%; overflow:hidden; border:4px solid rgba(255,255,255,0.1); background:#000; margin:0 auto;">
                          <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                      </div>
                      
                      <div style="margin-top:16px;">
                          <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">{{ __('messages.collection_progress') }}</div>
                          <div style="font-size:16px; font-weight:bold; color:var(--accent);">
                              {{ $ownedCount }} / {{ $totalObjects }}
                          </div>
                          <div style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:3px; margin-top:6px; overflow:hidden;">
                              <div style="width:{{ $progressPercent }}%; height:100%; background:var(--accent); border-radius:3px;"></div>
                          </div>
                          <div style="font-size:10px; color:var(--muted); margin-top:2px;">{{ $progressPercent }}% {{ __('messages.captured') }}</div>
                      </div>
                  </div>

                  <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin-bottom:24px;">

                  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <label>Profile Picture</label>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div style="width:64px; height:64px; border-radius:50%; overflow:hidden; background:#000;">
                                <img src="{{ $user->avatar_url }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <input type="file" name="avatar" accept="image/*" style="flex:1;">
                        </div>
                    </div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Name') }}</label><input name="name" value="{{ (string)old('name', $user->name) }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6; {{ !auth()->user()->is_admin ? 'opacity:0.6; cursor:not-allowed;' : '' }}" {{ !auth()->user()->is_admin ? 'readonly' : '' }}></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Display Name') }}</label><input name="display_name" value="{{ (string)old('display_name', $user->display_name ?? '') }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6; {{ !auth()->user()->is_admin ? 'opacity:0.6; cursor:not-allowed;' : '' }}" {{ !auth()->user()->is_admin ? 'readonly' : '' }}></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Full Name') }}</label><input name="full_name" value="{{ (string)old('full_name', $user->full_name ?? '') }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6;"></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Email') }}</label><input name="email" value="{{ (string)old('email', $user->email) }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6; opacity:0.6; cursor:not-allowed;" disabled></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Twitter') }}</label><input name="twitter" value="{{ (string)old('twitter', $user->twitter ?? '') }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6;"></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Instagram') }}</label><input name="instagram" value="{{ (string)old('instagram', $user->instagram ?? '') }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6;"></div>
                    <div class="form-row"><label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('Homepage') }}</label><input name="homepage" value="{{ (string)old('homepage', $user->homepage ?? '') }}" style="background:rgba(0,0,0,0.2); border-color:rgba(255,255,255,0.05); color:#e6eef6;"></div>
                    
                    <div style="margin-top:24px; border-top:1px solid rgba(255,255,255,0.05); padding-top:16px;">
                        <label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; display:block; margin-bottom:8px;">{{ __('My Equipment') }}</label>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($allScopes as $scope)
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                    <input type="checkbox" name="scopes[]" value="{{ $scope->id }}" {{ $user->scopes->contains($scope->id) ? 'checked' : '' }} style="width:auto; margin:0;">
                                    <span style="font-size:14px;">{{ $scope->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div style="margin-top:24px"><button type="submit" class="btn" style="width:100%; padding:10px;">{{ __('messages.save_profile') }}</button></div>
                  </form>
              </div>
          </div>
      </div>

      <!-- Middle: Collection Progress -->
      <div class="profile-col-center">
          <h2 style="margin-top:0; margin-bottom:24px;">{{ __('messages.collection_progress') }}</h2>
          
          <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:16px;">
              @foreach($objects as $obj)
                @php
                    $isOwned = $ownedImages->has($obj->id);
                    $img = $isOwned ? $ownedImages->get($obj->id) : null;
                @endphp
                <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; position:relative; opacity: {{ $isOwned ? '1' : '0.5' }}; border: {{ $isOwned ? '1px solid var(--accent)' : '1px solid transparent' }}; height:100%;">
                    
                    <!-- Thumbnail / Placeholder -->
                    <div style="aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#000; position:relative;">
                         @if($isOwned && $img)
                             <a href="{{ route('objects.show', $obj->id) }}" style="width:100%; height:100%; display:block;">
                                <img src="{{ $img->url }}" alt="{{ $img->filename }}" style="width:100%; height:100%; object-fit:cover;">
                             </a>
                             <!-- Green Check -->
                             <div style="position:absolute; top:4px; right:4px; background:#2ecc71; color:#fff; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.5);">✓</div>
                         @else
                             <!-- Placeholder text -->
                            <div style="text-align:center; padding:4px;">
                                <div style="font-weight:700; color:var(--muted); font-size:12px;">{{ $obj->catalog }}</div>
                                <div style="font-size:10px; color:var(--muted); word-break:break-word;">{{ \Illuminate\Support\Str::limit($obj->name, 20) }}</div>
                            </div>
                            <!-- Red Cross -->
                            <div style="position:absolute; top:4px; right:4px; background:#e74c3c; color:#fff; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.5);">✕</div>
                         @endif
                    </div>

                    <!-- Footer Info -->
                    <div style="padding:6px; background:rgba(255,255,255,0.02); flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                        <div style="font-weight:600; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $obj->name ?: $obj->catalog }}
                        </div>
                        @if($obj->name && $obj->catalog)
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $obj->catalog }}</div>
                        @endif
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                            <span style="font-size:10px; color:var(--muted);">{{ $isOwned ? __('messages.captured') : __('messages.missing') }}</span>
                            @if($isOwned && $img)
                                <form action="{{ route('images.destroy', $img->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#e74c3c; cursor:pointer; font-size:14px; padding:0; line-height:1;" title="{{ __('messages.delete') }}">&times;</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
              @endforeach
          </div>

          <div style="margin-top:24px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.03);">
            {{ $objects->links('vendor.pagination.board-arrows') }}
          </div>
      </div>

      <!-- Right Column: Community (Search & List) -->
      <div class="profile-col-right">
          <div class="card" style="padding:0; overflow:hidden; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
              <div style="background: linear-gradient(90deg, rgba(111,184,255,0.1), rgba(178,123,255,0.1)); padding:16px; border-bottom:1px solid rgba(255,255,255,0.05);">
                <h2 style="margin:0; font-size:16px; font-weight:600; color:#fff;">{{ __('messages.community') }}</h2>
              </div>
              <div style="padding:16px; border-bottom:1px solid rgba(255,255,255,0.05);">
                  <form method="GET" action="{{ route('profile.edit') }}" style="display:flex; gap:8px;">
                      <input type="text" name="user_q" value="{{ $uq ?? '' }}" placeholder="..." style="flex:1; padding:6px; font-size:12px; border-radius:4px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff; min-width:0;">
                      <button type="submit" style="background:rgba(255,255,255,0.1); border:none; color:#fff; border-radius:4px; font-size:12px; cursor:pointer; padding:0 8px;">🔍</button>
                  </form>
              </div>
              <div style="max-height: 500px; overflow-y: auto;">
                  @foreach($otherUsers as $u)
                    <div style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:600; font-size:14px; margin-bottom:2px;">
                            <a href="{{ route('profile.show', $u->id) }}" style="color:{{ $u->role_color }}; text-decoration:none;">{{ $u->display_name ?: $u->name }}</a>
                        </div>
                        @if($u->scopes->count() > 0)
                            <div style="font-size:11px; color:var(--muted); line-height:1.2;">
                                {{ $u->scopes->pluck('name')->join(', ') }}
                            </div>
                        @else
                            <div style="font-size:11px; color:rgba(255,255,255,0.2); font-style:italic;">
                                No equipment
                            </div>
                        @endif
                    </div>
                  @endforeach
              </div>
              <div style="padding:12px; font-size:12px; display:flex; justify-content:center;">
                 @if($otherUsers->hasPages())
                    <a href="{{ $otherUsers->previousPageUrl() }}" style="margin-right:8px; text-decoration:none; color:{{ $otherUsers->onFirstPage() ? 'var(--muted)' : 'var(--accent)' }}; pointer-events:{{ $otherUsers->onFirstPage() ? 'none' : 'auto' }};">◀</a>
                    <span style="color:var(--muted);">{{ $otherUsers->currentPage() }}</span>
                    <a href="{{ $otherUsers->nextPageUrl() }}" style="margin-left:8px; text-decoration:none; color:{{ $otherUsers->hasMorePages() ? 'var(--accent)' : 'var(--muted)' }}; pointer-events:{{ $otherUsers->hasMorePages() ? 'auto' : 'none' }};">▶</a>
                 @endif
              </div>
          </div>
      </div>
  </div>
@endsection
