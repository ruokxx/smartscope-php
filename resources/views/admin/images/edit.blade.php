@extends('admin.layouts.app')
@section('content')
  <h2>Edit image #{{ $image->id }}</h2>
  <form method="POST" action="{{ route('admin.images.update',$image->id) }}">
    @csrf @method('PUT')
    <div>Filename: {{ $image->filename }}</div>
    <div>Approved: <input type="checkbox" name="approved" value="1" {{ $image->approved ? 'checked':'' }}></div>
    <div>Processing software: <input name="processing_software" value="{{ old('processing_software',$image->processing_software) }}"></div>
    <div>Notes: <textarea name="notes">{{ old('notes',$image->notes) }}</textarea></div>
    <div><button type="submit">Save</button></div>
  </form>
@endsection
