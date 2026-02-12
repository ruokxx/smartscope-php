@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    <h2>Moderation Queue</h2>
    
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    @if($pendingImages->isEmpty())
        <p class="muted">No pending images.</p>
    @else
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:16px;">
            @foreach($pendingImages as $img)
                <div class="card" style="padding:16px; background:rgba(255,255,255,0.02);">
                    <div style="height:200px; background:#000; display:flex; align-items:center; justify-content:center; margin-bottom:12px; border-radius:8px; overflow:hidden;">
                        <a href="{{ $img->url }}" target="_blank">
                            <img src="{{ $img->url }}" style="max-height:100%; max-width:100%; object-fit:contain;">
                        </a>
                    </div>
                    <div style="font-weight:bold; margin-bottom:4px;">{{ $img->filename }}</div>
                    <div class="muted" style="font-size:12px; margin-bottom:8px;">
                        User: <span style="color:{{ $img->user->role_color }}">{{ $img->user->name ?? 'Unknown' }}</span><br>
                        Uploaded: {{ $img->created_at->diffForHumans() }}
                    </div>

                    <form method="POST" action="{{ route('admin.moderation.approve', $img->id) }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label style="font-size:12px; color:var(--muted);">Assign Object:</label>
                            <div class="styled-select-container">
                                <select name="object_id" class="styled-select" style="padding:6px 24px 6px 8px; font-size:12px;">
                                    <option value="">-- No Object --</option>
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ $img->object_id == $obj->id ? 'selected' : '' }}>
                                            {{ $obj->name ?: $obj->catalog }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="btn" style="flex:1; background:var(--accent);">Approve</button>
                    </form>
                            <form method="POST" action="{{ route('admin.moderation.reject', $img->id) }}" style="flex:1;" onsubmit="return confirm('Reject and delete this image?');">
                                @csrf
                                <button type="submit" class="btn" style="width:100%; background:var(--danger); border-color:var(--danger);">Reject</button>
                            </form>
                        </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
