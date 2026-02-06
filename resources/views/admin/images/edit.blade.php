@extends('admin.layouts.app')

@section('admin-content')
  <h2>Edit image #{{ $image->id }}</h2>

  <form method="POST" action="{{ route('admin.images.update',$image->id) }}">
    @csrf @method('PUT')

    <div class="form-row">
      <label>User</label>
      <select name="user_id">
        <option value="">-- none --</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" {{ $u->id == $image->user_id ? 'selected' : '' }}>{{ $u->email }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-row">
      <label>Object</label>
      <select name="object_id">
        <option value="">-- none --</option>
        @foreach($objects as $o)
          <option value="{{ $o->id }}" {{ $o->id == $image->object_id ? 'selected' : '' }}>{{ $o->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-row">
      <label>Scope</label>
      <select name="scope_id">
        <option value="">-- none --</option>
        @foreach($scopes as $s)
          <option value="{{ $s->id }}" {{ $s->id == $image->scope_id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-row">
      <label>Processing software</label>
      <input type="text" name="processing_software" value="{{ old('processing_software',$image->processing_software) }}">
    </div>

    <div class="form-row">
      <label>Notes</label>
      <textarea name="notes">{{ old('notes',$image->notes) }}</textarea>
    </div>

    <div class="form-row">
      <input type="hidden" name="approved" value="0">
      <label><input type="checkbox" name="approved" value="1" {{ $image->approved ? 'checked' : '' }}> Approved</label>
    </div>

    <div style="margin-top:8px">
      <button class="btn" type="submit">Save</button>
      <a href="{{ route('admin.images.index') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)">Cancel</a>
    </div>
  </form>
@endsection
