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

  <div class="panel">
    <table>
      <thead>
        <tr><th>ID</th><th>Thumb</th><th>Filename</th><th>User</th><th>Object</th><th>Scope</th><th>Approved</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @foreach($images as $img)
        <tr>
          <td>{{ $img->id }}</td>
          <td>@if($img->path)<img src="{{ Storage::url($img->path) }}" class="thumb">@endif</td>
          <td>{{ $img->filename }}</td>
          <td>{{ $img->user->email ?? '—' }}</td>
          <td>{{ $img->object->name ?? '—' }}</td>
          <td>{{ $img->scopeModel->name ?? '—' }}</td>
          <td>{{ $img->approved ? 'yes' : 'no' }}</td>
          <td class="actions">
            <a href="{{ route('admin.images.edit',$img->id) }}">Edit</a>
            <form action="{{ route('admin.images.destroy',$img->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button onclick="return confirm('Delete?')">Delete</button></form>
            @if(!$img->approved)
              <form action="{{ route('admin.images.approve',$img->id) }}" method="POST" style="display:inline">@csrf<button>Approve</button></form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top:12px">{{ $images->withQueryString()->links() }}</div>
  </div>
@endsection
