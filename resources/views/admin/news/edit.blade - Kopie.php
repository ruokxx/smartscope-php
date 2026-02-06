@extends('admin.layouts.app')
@section('admin-content')
  <h2>{{ $news->exists ? 'Edit' : 'Create' }} News</h2>
  <form method="POST" action="{{ $news->exists ? route('admin.news.update',$news->id) : route('admin.news.store') }}">
    @csrf
    @if($news->exists) @method('PUT') @endif

    <div><label>Title</label><input type="text" name="title" value="{{ old('title',$news->title) }}"></div>
    <div><label>Body</label><textarea name="body" rows="6">{{ old('body',$news->body) }}</textarea></div>
    <div><label><input type="checkbox" name="published" value="1" {{ old('published',$news->published) ? 'checked':'' }}> Published</label></div>
    <div style="margin-top:8px"><button type="submit" class="btn">Save</button></div>
  </form>
@endsection
