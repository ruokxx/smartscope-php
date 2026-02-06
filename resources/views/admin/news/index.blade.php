@extends('admin.layouts.app')
@section('admin-content')
  <h2>News</h2>
  <a href="{{ route('admin.news.create') }}" class="btn">Create news</a>
  <table style="width:100%;margin-top:12px">
    <thead><tr><th>Title</th><th>Published</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($news as $n)
      <tr>
        <td>{{ $n->title }}</td>
        <td>{{ $n->published ? 'yes':'no' }}</td>
        <td>{{ $n->created_at->format('Y-m-d') }}</td>
        <td>
          <a href="{{ route('admin.news.edit',$n->id) }}">Edit</a>
          <form action="{{ route('admin.news.destroy',$n->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button onclick="return confirm('Delete?')">Delete</button></form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $news->links() }}
@endsection
