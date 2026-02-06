<script>
  // simple client-side live filter for the current page results
  (function(){
    const input = document.querySelector('input[name="q"]');
    const rows = () => Array.from(document.querySelectorAll('table tbody tr'));
    input?.addEventListener('input', function(){
      const v = this.value.toLowerCase();
      rows().forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(v) ? '' : 'none';
      });
    });
  })();
</script>


@extends('layouts.app')

@section('content')
  <div class="card full">
    <h2>All Deep‑Sky Objects</h2>

    <form method="GET" style="margin:12px 0; display:flex; gap:8px; align-items:center;">
      <input type="search" name="q" value="{{ old('q', $q) }}" placeholder="Search name, catalog, type or description" style="flex:1;padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.02);color:#e6eef6">
      <button class="btn" type="submit">Search</button>
      <a href="{{ route('board') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)">Reset</a>
    </form>

    <table style="width:100%;border-collapse:collapse;margin-top:12px">
      <thead>
        <tr>
          <th style="text-align:left;padding:8px">Name</th>
          <th style="text-align:left;padding:8px">Catalog</th>
          <th style="text-align:left;padding:8px">Type</th>
          <th style="text-align:left;padding:8px">Owned</th>
          <th style="text-align:left;padding:8px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($objects as $o)
        <tr>
          <td style="padding:8px">{{ $o->name }}</td>
          <td style="padding:8px">{{ $o->catalog }}</td>
          <td style="padding:8px">{{ $o->type }}</td>
          <td style="padding:8px">
            @if(isset($owned[$o->id]))
              <img src="{{ Storage::url($owned[$o->id]->path) }}" alt="{{ $owned[$o->id]->filename }}" style="max-width:90px;border-radius:6px;">
            @else
              <span class="muted">—</span>
            @endif
          </td>
          <td style="padding:8px">
            <a href="{{ route('objects.show', $o->id) }}">View</a>
            @auth
              | <a href="{{ route('images.create') }}?object_id={{ $o->id }}">Upload</a>
            @endauth
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

  <div style="margin-top:12px">
  {!! $objects->links('pagination::bootstrap-4') !!}
</div>

  </div>
@endsection
