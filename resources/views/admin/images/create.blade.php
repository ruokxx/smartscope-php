
@extends('layouts.app')

@section('content')
Create Image (Admin)

  @if ($errors->any())
    

    @foreach ($errors->all() as $e)
    {{ $e }}
    @endforeach 

@endif

@csrf 




<form action="{{ route('admin.images.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label for="user_id">User (optional)</label>
    <select name="user_id" id="user_id" class="form-control">
      <option value="">-- select user --</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name ?? $u->email }}</option>
      @endforeach
    </select>
  </div>

  <div class="form-group">
    <label for="object_id">Object (optional)</label>
    <select name="object_id" id="object_id" class="form-control">
      <option value="">-- select object --</option>
      @foreach($objects as $o)
        <option value="{{ $o->id }}" {{ old('object_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="form-group">
    <label for="scope_id">Scope (optional)</label>
    <select name="scope_id" id="scope_id" class="form-control">
      <option value="">-- select scope --</option>
      @foreach($scopes as $s)
        <option value="{{ $s->id }}" {{ old('scope_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="form-group">
    <label for="image">Image (jpg, png) — max 50MB</label>
    <input type="file" name="image" id="image" class="form-control-file" accept=".jpg,.jpeg,.png" required>
  </div>

  <div class="form-group">
    <label for="exposure_total_seconds">Exposure total seconds</label>
    <input type="text" name="exposure_total_seconds" id="exposure_total_seconds" class="form-control" value="{{ old('exposure_total_seconds') }}">
  </div>

  <div class="form-group">
    <label for="number_of_subs">Number of subs</label>
    <input type="number" name="number_of_subs" id="number_of_subs" class="form-control" value="{{ old('number_of_subs') }}" min="1" step="1">
  </div>

  <div class="form-group">
    <label for="processing_software">Processing software</label>
    <input type="text" name="processing_software" id="processing_software" class="form-control" value="{{ old('processing_software') }}">
  </div>

  <div class="form-group">
    <label for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
  </div>

  <button class="btn btn-primary" type="submit">Upload</button>
</form>


