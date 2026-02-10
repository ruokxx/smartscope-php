<script>
  // simple client-side live filter for the current page results
  (function(){
    const input = document.querySelector('input[name="q"]');
    const cards = () => Array.from(document.querySelectorAll('.card').values()); // .card full is the container, we need the children of the grid
    // Actually the grid cards are inside the grid container.
    // Let's target the grid items specifically. The grid container doesn't have a class but the items are div.card.
    // The main container is also .card.full.
    // Let's add a class to the grid items or target them more specifically.
    // In the previous step I added: <div class="card" style="padding:0; ..."> inside the grid.
    
    input?.addEventListener('input', function(){
      const v = this.value.toLowerCase();
      const gridCards = document.querySelectorAll('.grid-item'); 
      gridCards.forEach(c => {
        const text = c.innerText.toLowerCase();
        c.style.display = text.includes(v) ? 'flex' : 'none';
      });
    });
  })();
</script>


@extends('layouts.app')

@section('content')
  <div class="card full">
    <h2>{{ __('messages.collection') }}</h2>

    <form method="GET" style="margin:12px 0; display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
      <input type="search" name="q" value="{{ old('q', $q) }}" placeholder="{{ __('messages.search_placeholder') }}" style="flex:1;padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.02);color:#e6eef6">
      <button class="btn" type="submit">{{ __('messages.search') }}</button>
      <a href="{{ route('board') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)">{{ __('messages.reset') }}</a>
    </form>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:16px; margin-top:24px;">
      @foreach($objects as $o)
        @php
            $isOwned = isset($owned[$o->id]);
            $img = $isOwned ? $owned[$o->id] : null;
            $isGuest = !Auth::check();
        @endphp
        <div class="card grid-item" style="padding:0; overflow:hidden; display:flex; flex-direction:column; position:relative; opacity: {{ ($isOwned || $isGuest) ? '1' : '0.5' }}; border: {{ $isOwned ? '1px solid var(--accent)' : '1px solid transparent' }}; height:100%;">
            
            <!-- Thumbnail / Placeholder -->
            <div style="aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#000; position:relative;">
                 @if($img)
                     <a href="{{ route('objects.show', $o->id) }}" style="width:100%; height:100%; display:block;">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($img->path) }}" alt="{{ $img->filename }}" style="width:100%; height:100%; object-fit:cover;">
                     </a>
                     @auth
                        <!-- Green Check only for logged in users -->
                        <div style="position:absolute; top:4px; right:4px; background:#2ecc71; color:#fff; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.5);">✓</div>
                     @endauth
                 @else
                     <a href="{{ route('objects.show', $o->id) }}" style="text-decoration:none; width:100%; height:100%; display:flex; align-items:center; justify-content:center; flex-direction:column;">
                        <!-- Placeholder text -->
                        <div style="text-align:center; padding:4px;">
                            <div style="font-weight:700; color:var(--muted); font-size:12px;">{{ $o->catalog }}</div>
                            <div style="font-size:10px; color:var(--muted); word-break:break-word;">{{ \Illuminate\Support\Str::limit($o->name, 20) }}</div>
                        </div>
                     </a>
                     @auth
                        <!-- Red Cross only for logged in users -->
                        <div style="position:absolute; top:4px; right:4px; background:#e74c3c; color:#fff; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.5);">✕</div>
                     @endauth
                 @endif
            </div>

            <!-- Footer Info -->
            <div style="padding:6px; background:rgba(255,255,255,0.02); flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                <div style="font-weight:600; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <a href="{{ route('objects.show', $o->id) }}" style="color:inherit; text-decoration:none;">{{ $o->name ?: $o->catalog }}</a>
                </div>
                @if($o->name && $o->catalog)
                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $o->catalog }}</div>
                @endif
                
                @if($img && $img->user)
                   <div style="font-size:10px; color:var(--muted); margin-top:2px;">
                        @auth
                            by <a href="{{ route('profile.show', $img->user->id) }}" style="color:{{ $img->user->role_color }}; text-decoration:none;">{{ $img->user->display_name ?: $img->user->name }}</a>
                        @else
                            by <span style="color:{{ $img->user->role_color }}">{{ $img->user->display_name ?: $img->user->name }}</span>
                        @endauth
                   </div>
                @endif

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                    @auth
                        <span style="font-size:10px; color:var(--muted);">{{ $isOwned ? __('messages.captured') : __('messages.missing') }}</span>
                    @else
                         <span style="font-size:10px; color:var(--muted);">{{ $o->type }}</span>
                    @endauth

                    @auth
                        @if(!$isOwned)
                            <a href="{{ route('images.create') }}?object_id={{ $o->id }}" title="{{ __('messages.upload') }}" style="color:var(--accent); text-decoration:none; font-size:14px; line-height:1;">⬆</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
      @endforeach
    </div>

    <div style="margin-top:24px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.03);">
      {{ $objects->links('vendor.pagination.board-arrows') }}
    </div>

  </div>
@endsection
