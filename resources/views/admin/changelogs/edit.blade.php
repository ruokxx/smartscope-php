@extends('admin.layouts.app')

@section('admin-content')
  <h2>{{ $changelog->exists ? 'Edit Changelog' : 'Create Changelog' }}</h2>

  <form method="POST" action="{{ $changelog->exists ? route('admin.changelogs.update', $changelog->id) : route('admin.changelogs.store') }}">
    @csrf
    @if($changelog->exists) @method('PUT') @endif

    <div style="margin-bottom:12px">
      <label style="display:block;color:var(--muted)">Title</label>
      <input type="text" name="title" value="{{ old('title', $changelog->title) }}" required />
    </div>

    <div style="margin-bottom:12px">
      <label style="display:block;color:var(--muted)">Version (optional)</label>
      <input type="text" name="version" value="{{ old('version', $changelog->version) }}" placeholder="e.g. v1.2.0" />
    </div>

    <div style="margin-bottom:12px">
      <label style="display:block;color:var(--muted)">Body</label>
      <textarea name="body" rows="10" required>{{ old('body', $changelog->body) }}</textarea>
    </div>

    <div style="margin-bottom:12px">
      <label style="display:inline-flex;align-items:center;color:var(--muted)">
        <input type="hidden" name="published" value="0">
        <input type="checkbox" name="published" value="1" {{ old('published', $changelog->published_at ? 1 : 0) ? 'checked' : '' }} style="margin-right:8px">
        Published
      </label>
    </div>

    <div style="margin-top:16px">
      <button type="submit" class="btn">{{ $changelog->exists ? 'Save' : 'Create' }}</button>
      <a href="{{ route('admin.changelogs.index') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Cancel</a>
    </div>
  </form>
@endsection
