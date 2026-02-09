@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
  <h2>Upload Image (Admin)</h2>

  @if ($errors->any())
    <div class="notice" style="background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.2); color:#fca5a5;">
        <ul style="margin:0; padding-left:20px;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
  @endif

  <form action="{{ route('admin.images.store') }}" method="POST" enctype="multipart/form-data" class="upload-form" style="max-width:100%;">
    @csrf

    <div class="row-2">
        <div class="form-row">
            <label for="user_id">User</label>
            <div class="styled-select-container">
                <select name="user_id" id="user_id" class="styled-select">
                    <option value="">-- Assign to User (Optional) --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name ?? $u->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <label for="object_id">Target Object</label>
             <div class="styled-select-container">
                <select name="object_id" id="object_id" class="styled-select">
                    <option value="">-- Select Object --</option>
                    @foreach($objects as $o)
                        <option value="{{ $o->id }}" {{ old('object_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row-2">
         <div class="form-row">
            <label for="scope_id">Scope Used</label>
             <div class="styled-select-container">
                <select name="scope_id" id="scope_id" class="styled-select">
                    <option value="">-- Select Scope --</option>
                    @foreach($scopes as $s)
                        <option value="{{ $s->id }}" {{ old('scope_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
             <label for="image">Image File (JPG, PNG)</label>
             <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png" required style="padding:6px;">
        </div>
    </div>

    <div class="accent-line"></div>

    <div class="row-2">
        <div class="form-row">
            <label for="exposure_total_seconds">Exposure (seconds)</label>
            <input type="text" name="exposure_total_seconds" id="exposure_total_seconds" value="{{ old('exposure_total_seconds') }}" placeholder="e.g. 3600">
        </div>
        <div class="form-row">
             <label for="number_of_subs">Number of Subs</label>
             <input type="number" name="number_of_subs" id="number_of_subs" value="{{ old('number_of_subs') }}" min="1">
        </div>
    </div>

    <div class="form-row">
        <label for="processing_software">Processing Software</label>
        <input type="text" name="processing_software" id="processing_software" value="{{ old('processing_software') }}" placeholder="e.g. PixInsight, Photoshop">
    </div>

    <div class="form-row">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="4">{{ old('notes') }}</textarea>
    </div>

    <div style="margin-top:20px;">
        <button class="btn" type="submit" style="padding:10px 24px; font-size:14px;">Upload Image</button>
    </div>
  </form>
</div>
@endsection
