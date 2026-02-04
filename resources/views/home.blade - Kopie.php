@extends('layouts.app')

@section('content')
  <h2>Latest uploads (last 2 hours)</h2>
  <div class="grid">
    @forelse($images as $img)
      <div class="card">
        <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}">
        <div><strong>Filename:</strong> {{ $img->filename }}</div>
        <div><strong>Uploaded:</strong> {{ $img->upload_time->format('d.m.Y, H:i:s') }}</div>
        <div><strong>User:</strong> {{ $img->user->name ?? '—' }}</div>
      </div>
    @empty
      <p>No recent uploads.</p>
    @endforelse
  </div>
@endsection
