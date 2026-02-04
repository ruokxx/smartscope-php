@extends('layouts.app')

@section('content')
  <h2>Latest uploads (last 2 hours)</h2>

  <div class="grid" style="margin-top:12px">
    @forelse($images as $img)
      <div class="card thumb-small" style="width:180px; padding:8px; text-align:center;">
        <div style="height:110px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
          <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}" style="max-height:100%; max-width:100%; object-fit:cover;">
        </div>
        <div style="margin-top:8px; font-size:13px;">
          <div style="font-weight:600;">{{ \Illuminate\Support\Str::limit($img->filename, 18) }}</div>
          <div class="muted" style="font-size:12px;">{{ $img->user->name ?? '—' }}</div>
          <div class="muted" style="font-size:11px;">{{ optional($img->upload_time)->format('d.m.Y H:i') ?? '' }}</div>
        </div>
      </div>
    @empty
      <p>No recent uploads.</p>
    @endforelse
  </div>
@endsection
