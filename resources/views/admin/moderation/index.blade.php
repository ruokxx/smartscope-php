@extends('layouts.app')

@section('content')
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
                        <a href="{{ Storage::url($img->path) }}" target="_blank">
                            <img src="{{ Storage::url($img->path) }}" style="max-height:100%; max-width:100%; object-fit:contain;">
                        </a>
                    </div>
                    <div style="font-weight:bold; margin-bottom:4px;">{{ $img->filename }}</div>
                    <div class="muted" style="font-size:12px; margin-bottom:8px;">
                        User: {{ $img->user->name ?? 'Unknown' }}<br>
                        Object: {{ $img->object->name ?? 'None' }}<br>
                        Uploaded: {{ $img->created_at->diffForHumans() }}
                    </div>
                    
                    <div style="display:flex; gap:8px;">
                        <form method="POST" action="{{ route('admin.moderation.approve', $img->id) }}" style="flex:1;">
                            @csrf
                            <button type="submit" class="btn" style="width:100%; background:var(--accent);">Approve</button>
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
