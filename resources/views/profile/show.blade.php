@extends('layouts.app')

@section('content')
<div class="row-2" style="display:flex; flex-wrap:wrap; align-items:start; gap:32px;">
    <!-- Profile Info Card -->
    <div class="card" style="padding:0; min-width:300px; flex:0 0 300px; overflow:hidden; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
        <div style="background: linear-gradient(90deg, rgba(111,184,255,0.1), rgba(178,123,255,0.1)); padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.05);">
            <h2 style="margin:0; font-size:18px; font-weight:600; color:#fff;">{{ $user->display_name ?: $user->name }}</h2>
            @if($user->full_name)
                <div style="font-size:13px; color:var(--muted); margin-top:4px;">{{ $user->full_name }}</div>
            @endif
        </div>
        <div style="padding:24px;">
            <!-- Equipment List -->
            @if($user->scopes->count() > 0)
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--accent); margin-top:0;">{{ __('messages.equipment') }}</h3>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:8px;">
                        @foreach($user->scopes as $scope)
                            <li style="display:flex; align-items:center; gap:8px; font-size:14px;">
                                <span style="width:6px; height:6px; background:var(--accent); border-radius:50%; display:inline-block;"></span>
                                {{ $scope->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Social / Web -->
            @if($user->homepage || $user->twitter || $user->instagram)
                <div style="border-top:1px solid rgba(255,255,255,0.05); paddingTop:16px;">
                    @if($user->homepage)
                        <div style="margin-bottom:8px;"><a href="{{ $user->homepage }}" target="_blank" style="color:var(--muted); text-decoration:none; font-size:14px;">🌐 {{ parse_url($user->homepage, PHP_URL_HOST) }}</a></div>
                    @endif
                    @if($user->twitter)
                        <div style="margin-bottom:8px;"><a href="https://twitter.com/{{ $user->twitter }}" target="_blank" style="color:var(--muted); text-decoration:none; font-size:14px;">🐦 @ {{ $user->twitter }}</a></div>
                    @endif
                    @if($user->instagram)
                        <div><a href="https://instagram.com/{{ $user->instagram }}" target="_blank" style="color:var(--muted); text-decoration:none; font-size:14px;">📸 @ {{ $user->instagram }}</a></div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Collection Progress -->
    <div style="flex:1; min-width:0;">
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
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($img->path) }}" alt="{{ $img->filename }}" style="width:100%; height:100%; object-fit:cover;">
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
                        <a href="{{ route('objects.show', $obj->id) }}" style="color:inherit; text-decoration:none;">{{ $obj->name ?: $obj->catalog }}</a>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                        <span style="font-size:10px; color:var(--muted);">{{ $isOwned ? __('messages.captured') : __('messages.missing') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
