@extends('admin.layouts.app')
@section('content')
  <h2>Images</h2>
  <form method="GET"><input name="q" value="{{ $q }}" placeholder="search filename"><button>Search</button></form>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Thumb</th><th>Filename</th><th>User</th><th>Object</th><th>Approved</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($images as $img)
      <tr>
        <td>{{ $img->id }}</td>
        <td><img src="{{ Storage::url($img->path) }}" style="max-width:120px"></td>
        <td>{{ $img->filename }}</td>
        <td>{{ $img->user->email ?? '—' }}</td>
        <td>{{ $img->object->name ?? '—' }}</td>
        <td>{{ $img->approved ? 'yes':'no' }}</td>
        <td>
          <a href="{{ route('admin.images.edit',$img->id) }}">Edit</a>
          <form method="POST" action="{{ route('admin.images.destroy',$img->id) }}" style="display:inline">@csrf @method('DELETE')<button onclick="return confirm('Delete?')">Delete</button></form>
          @if(!$img->approved)
            <form method="POST" action="{{ route('admin.images.approve',$img->id) }}" style="display:inline">@csrf<button>Approve</button></form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $images->withQueryString()->links() }}
@endsection
