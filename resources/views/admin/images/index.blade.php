@extends('admin.layouts.app')

@section('admin-content')
  <h2>Images</h2>

  <div style="margin:12px 0">
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="search filename..." style="padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.02);color:#e6eef6">
      <button class="btn" type="submit">Search</button>
      <a href="{{ route('admin.images.create') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Create</a>
    </form>
  </div>

  <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:16px;">
    @foreach($images as $img)
      <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; background:rgba(255,255,255,0.03);">
        <div style="aspect-ratio:1/1; background:#000; position:relative;">
            @if($img->path)
                <a href="{{ $img->url }}" target="_blank">
                    <img src="{{ $img->url }}" style="width:100%; height:100%; object-fit:cover;">
                </a>
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted)">No Image</div>
            @endif
            <div style="position:absolute; top:4px; right:4px; background:rgba(0,0,0,0.6); color:#fff; padding:2px 6px; border-radius:4px; font-size:10px;">ID: {{ $img->id }}</div>
        </div>
        <div style="padding:12px; flex:1; display:flex; flex-direction:column; gap:4px;">
            <div style="font-weight:bold; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $img->filename }}">{{ $img->filename }}</div>
            <div style="font-size:12px; color:var(--muted);">User: <span style="color:{{ $img->user->role_color }}">{{ $img->user->name ?? '—' }}</span></div>
            <div style="font-size:12px; color:var(--muted);">Obj: {{ $img->object->name ?? '—' }}</div>
            <div style="font-size:12px; color:var(--muted);">
                Status: 
                @if($img->approved) <span style="color:#2ecc71">Approved</span>
                @else <span style="color:#e74c3c">Pending</span> @endif
            </div>

            <div style="margin-top:auto; padding-top:12px; display:flex; gap:6px; flex-wrap:wrap;">
                 <a href="{{ route('admin.images.edit',$img->id) }}" class="btn" style="padding:4px 8px; font-size:11px; background:rgba(255,255,255,0.1); color:#fff;">Edit</a>
                 
                 @if(!$img->approved)
                    <form action="{{ route('admin.images.approve',$img->id) }}" method="POST" style="display:inline">
                        @csrf <button class="btn" style="padding:4px 8px; font-size:11px; background:var(--accent);">Approve</button>
                    </form>
                 @endif

                 <form action="{{ route('admin.images.destroy',$img->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn" style="padding:4px 8px; font-size:11px; background:var(--danger); color:#fff;" onclick="return confirm('Delete?')">Del</button>
                 </form>
            </div>
        </div>
      </div>
    @endforeach
  </div>

  <div style="margin-top:20px">{{ $images->withQueryString()->links() }}</div>
@endsection
