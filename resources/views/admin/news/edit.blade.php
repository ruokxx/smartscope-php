@extends('admin.layouts.app')

@section('admin-content')
  <h2>{{ $news->exists ? 'Edit news' : 'Create news' }}</h2>

  <form method="POST" action="{{ $news->exists ? route('admin.news.update', $news->id) : route('admin.news.store') }}">
    @csrf
    @if($news->exists) @method('PUT') @endif

    <div style="margin-bottom:12px">
      <label style="display:block;color:var(--muted)">Title</label>
      <input type="text" name="title" value="{{ old('title', $news->title) }}" required />
    </div>

    <div style="margin-bottom:12px">
      <label style="display:block;color:var(--muted)">Body</label>
      <textarea name="body" rows="8">{{ old('body', $news->body) }}</textarea>
    </div>

    <div style="margin-bottom:12px">
      <label style="display:inline-flex;align-items:center;color:var(--muted)">
        <input type="hidden" name="published" value="0">
        <input type="checkbox" name="published" value="1" {{ old('published', $news->published) ? 'checked' : '' }} style="margin-right:8px">
        Published
      </label>
    </div>

    <div style="margin-top:8px">
      <button type="submit" class="btn">{{ $news->exists ? 'Save' : 'Create' }}</button>
      <a href="{{ route('admin.news.index') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Cancel</a>
    </div>
  </form>
@endsection
