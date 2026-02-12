@extends('layouts.app')

@section('content')
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h2 style="margin:0;">Compare: {{ $object->name }}</h2>
      <a href="{{ route('objects.show', $object->id) }}" class="btn" style="background:transparent; border:1px solid rgba(255,255,255,0.1); color:var(--muted);">Back to Object</a>
  </div>

  <!-- Filter Form -->
  <div class="card" style="padding:16px; margin-bottom:24px;">
      <form method="GET" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start;">
          <div style="display:flex; flex-direction:column;">
              <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">Min. Exposure (min)</label>
              <input type="number" name="min_exposure" value="{{ request('min_exposure') }}" placeholder="Alle anzeigen" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
          </div>
          
          <div style="display:flex; flex-direction:column;">
              <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">Filter</label>
              <select name="filter" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
                  <option value="" style="color:#000;">Alle anzeigen</option>
                  @foreach($filters as $f)
                      <option value="{{ $f }}" {{ request('filter') == $f ? 'selected' : '' }} style="color:#000;">{{ $f }}</option>
                  @endforeach
              </select>
          </div>

          <div style="display:flex; flex-direction:column;">
              <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">Gain</label>
              <input type="number" name="gain" value="{{ request('gain') }}" placeholder="Alle anzeigen" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
          </div>

          <div style="display:flex; flex-direction:column;">
              <label style="display:block; font-size:12px; margin-bottom:4px; color:transparent; height:15px; line-height:15px; overflow:hidden; white-space:nowrap; user-select:none;">Action</label>
              <div style="display:flex; gap:8px;">
                  <button type="submit" class="btn" style="margin:0; display:block; background:var(--accent); color:#fff; padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid transparent; box-sizing:border-box;">Apply Filters</button>
                  @if(request()->anyFilled(['min_exposure', 'filter', 'gain']))
                      <a href="{{ route('compare.show', $object->id) }}" class="btn" style="margin:0; display:block; background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.1); padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; text-decoration:none; box-sizing:border-box;">Reset</a>
                  @endif
              </div>
          </div>
      </form>
  </div>
  <div style="display:flex;gap:12px">
    <div class="card" style="flex:1;text-align:center">
      <h3>Dwarf</h3>
      @if($dwarf)
        <div class="card thumb-small" style="flex:1; margin:0; padding:0; overflow:hidden; border-radius:8px;">
          <img class="thumb" src="{{ $dwarf->url }}">
        </div>
        <div>Uploaded: {{ $dwarf->upload_time }}</div>
        <div>Software: {{ $dwarf->processing_software }}</div>
      @else
        <div class="muted">No Dwarf image</div>
      @endif
    </div>

    <div>VS</div>

    <div class="card" style="flex:1;text-align:center">
      <h3>Seestar</h3>
      @if($seestar)
        <div class="card thumb-small" style="flex:1; margin:0; padding:0; overflow:hidden; border-radius:8px;">
          <div style="position:absolute; top:4px; left:4px; font-size:10px; color:#fff; background:rgba(0,0,0,0.5); padding:2px 4px; border-radius:4px;">Seestar S50</div>
          <img class="thumb" src="{{ $seestar->url }}">
        </div>
        <div>Uploaded: {{ $seestar->upload_time }}</div>
        <div>Software: {{ $seestar->processing_software }}</div>
      @else
        <div class="muted">No Seestar image</div>
      @endif
    </div>
  </div>
@endsection
