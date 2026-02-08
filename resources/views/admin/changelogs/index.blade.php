@extends('admin.layouts.app')
@section('admin-content')
  <h2>Changelogs</h2>
  <div style="margin:12px 0">
      <a href="{{ route('admin.changelogs.create') }}" class="btn">Create Changelog</a>
  </div>
  <table style="width:100%;margin-top:12px">
    <thead><tr><th>Title</th><th>Version</th><th>Published</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($changelogs as $c)
      <tr>
        <td>{{ $c->title }}</td>
        <td>{{ $c->version ?? '—' }}</td>
        <td>{{ $c->published_at ? 'yes' : 'no' }}</td>
        <td>{{ $c->created_at->format('Y-m-d') }}</td>
        <td>
          <a href="{{ route('admin.changelogs.edit', $c->id) }}">Edit</a>
          <form action="{{ route('admin.changelogs.destroy', $c->id) }}" method="POST" style="display:inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Delete?')" style="background:transparent;border:none;color:var(--muted);text-decoration:underline;cursor:pointer">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div style="margin-top:12px">{{ $changelogs->links() }}</div>
@endsection
