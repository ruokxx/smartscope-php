@extends('layouts.app')

@section('content')
  <div class="card" style="padding:0; overflow:hidden; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.02);">
    <!-- Gradient Header -->
    <div style="background: linear-gradient(90deg, rgba(111,184,255,0.1), rgba(178,123,255,0.1)); padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.05);">
      <h2 style="margin:0; font-size:18px; font-weight:600; color:#fff;">
        {{ $obj->name }} 
        @if($obj->catalog) 
            <span style="opacity:0.7; font-weight:400;">— {{ $obj->catalog }}</span> 
        @endif
      </h2>
    </div>

    <!-- Content Body -->
    <div style="padding:24px;">
      <p class="muted" style="margin-top:0; line-height:1.6;">{{ $obj->description }}</p>

      <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
        <label style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('messages.show_images_by_user') }}</label>
        <select id="uploaderSelect" style="background:#1a2634; border:1px solid rgba(255,255,255,0.1); color:#e6eef6; padding:6px; border-radius:4px; margin-left:8px;">
          <option value="all" style="background:#1a2634; color:#e6eef6;">{{ __('messages.all_users') }}</option>
          @foreach($uploaders as $u)
            <option value="{{ $u->id }}" style="background:#1a2634; color:#e6eef6;">{{ $u->name ?? $u->email }}</option>
          @endforeach
        </select>

        @auth
          <label style="margin-left:24px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7;">{{ __('messages.your_images') }}</label>
          <select id="myImageSelect" style="background:#1a2634; border:1px solid rgba(255,255,255,0.1); color:#e6eef6; padding:6px; border-radius:4px; margin-left:8px;">
            <option value="" style="background:#1a2634; color:#e6eef6;">{{ __('messages.none') }}</option>
            @foreach($myImages as $mi)
            <option value="{{ $mi->id }}" style="background:#1a2634; color:#e6eef6;">My: {{ $obj->catalog ?? $obj->name }} ({{ $mi->upload_time->format('Y-m-d H:i') }})</option>
            @endforeach
          </select>
        @endauth

        <button id="compareBtn" class="btn" style="margin-left:16px">{{ __('messages.compare_selected') }}</button>
      </div>

    <hr />

    <div id="imagesContainer" class="grid" style="margin-top:12px">
      @foreach($imagesByUser->flatten(1) as $img)
        <div class="card thumb-small image-card" data-user-id="{{ $img->user->id ?? 0 }}" data-image-id="{{ $img->id }}">
          <a href="{{ Storage::url($img->path) }}" target="_blank" title="{{ __('messages.open_full_res') }}">
            <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $obj->name }}">
          </a>
          <div style="margin-top:6px"><strong>{{ $obj->catalog ?? $obj->name }}</strong></div>
          <div class="muted">{{ __('messages.by') }}: {{ $img->user->name ?? $img->user->email ?? 'guest' }}</div>
          <div class="muted">{{ __('messages.scope') }}: {{ $img->scopeModel->name ?? '-' }}</div>
          
          <div style="margin-top:8px; font-size:11px; color:var(--muted); line-height:1.4; border-top:1px solid rgba(255,255,255,0.05); padding-top:4px;">
            @if($img->sub_exposure_time || $img->number_of_subs)
              <div>
                <span style="color:var(--accent)">{{ __('messages.integration') }}:</span> 
                {{ $img->number_of_subs ?? '?' }} x {{ $img->sub_exposure_time ?? $img->sub_exposure_seconds ?? '?' }}s
                @if($img->exposure_total_seconds)
                  @php
                    $h = floor($img->exposure_total_seconds / 3600);
                    $m = floor(($img->exposure_total_seconds % 3600) / 60);
                  @endphp
                  ({{ $h }}h {{ $m }}m)
                @endif
              </div>
            @endif
            @if($img->gain || $img->iso_or_gain)
              <div><span style="color:var(--accent)">{{ __('messages.gain') }}:</span> {{ $img->gain ?? $img->iso_or_gain }}</div>
            @endif
            @if($img->filter)
              <div><span style="color:var(--accent)">{{ __('messages.filter') }}:</span> {{ $img->filter }}</div>
            @endif
            @if($img->bortle)
              <div><span style="color:var(--accent)">{{ __('messages.bortle') }}:</span> {{ $img->bortle }}</div>
            @endif
             @if($img->seeing)
              <div><span style="color:var(--accent)">{{ __('messages.seeing') }}:</span> {{ $img->seeing }}</div>
            @endif
             @if($img->session_date)
              <div><span style="color:var(--accent)">{{ __('messages.session_date') }}:</span> {{ $img->session_date->format('Y-m-d') }}</div>
            @endif
          </div>

          <div style="margin-top:6px">
            <label><input type="checkbox" class="select-compare" value="{{ $img->id }}"> {{ __('messages.select') }}</label>
            @auth
              @if($img->user && $img->user->id == auth()->id())
                <span class="muted" style="margin-left:6px">({{ __('messages.yours') }})</span>
              @endif
            @endauth
          </div>
        </div>
      @endforeach
    </div>

    <!-- Compare modal/area -->
    <div id="compareArea" style="display:none;margin-top:18px">
      <h3>{{ __('messages.compare') }}</h3>
      <div style="display:flex;gap:12px">
        <div id="compareLeft" class="card full" style="flex:1"></div>
        <div id="compareRight" class="card full" style="flex:1"></div>
      </div>
    </div>
  </div>

  <script>
    (function(){
const images = Array.from(document.querySelectorAll('.image-card[data-image-id]'));
const uploaderSelect = document.getElementById('uploaderSelect');
      const myImageSelect = document.getElementById('myImageSelect');
      const compareBtn = document.getElementById('compareBtn');
      const compareArea = document.getElementById('compareArea');
      const compareLeft = document.getElementById('compareLeft');
      const compareRight = document.getElementById('compareRight');

// robustes Setup: finde alle cards die ein data-image-id besitzen
// const images = Array.from(document.querySelectorAll('.image-card[data-image-id]'));
//const uploaderSelect = document.getElementById('uploaderSelect');

function filterByUser(userId){
  images.forEach(card => {
    const uid = card.getAttribute('data-user-id') || '0';
    // sichtbarkeit: alle oder match
    if(userId === 'all' || String(uid) === String(userId)) {
      card.style.display = '';
      // falls img lazy o.ä., stelle sicher src gesetzt ist
      const img = card.querySelector('img.thumb');
      if(img && !img.src) img.src = img.dataset.src || img.getAttribute('data-src') || img.getAttribute('src');
    } else {
      card.style.display = 'none';
    }
  });
}

// safe init: nur wenn uploaderSelect existiert
if (uploaderSelect) {
  uploaderSelect.addEventListener('change', function(){
    filterByUser(this.value);
  });
}


      if (myImageSelect){
        myImageSelect.addEventListener('change', function(){
          if (this.value){
            filterByUser('{{ auth()->id() ?? "" }}');
            images.forEach(card => {
              const iid = card.getAttribute('data-image-id');
              const cb = card.querySelector('.select-compare');
              if (cb) cb.checked = (iid === this.value);
            });
          } else {
            filterByUser(uploaderSelect.value);
            images.forEach(card => { const cb = card.querySelector('.select-compare'); if(cb) cb.checked = false; });
          }
        });
      }

      compareBtn.addEventListener('click', function(){
        const selected = Array.from(document.querySelectorAll('.select-compare:checked')).map(i=>i.value);
        if (selected.length < 2){
          alert('{{ __('messages.alert_select_two') }}');
          return;
        }
        const leftCard = document.querySelector('.image-card[data-image-id="'+selected[0]+'"]');
        const rightCard = document.querySelector('.image-card[data-image-id="'+selected[1]+'"]');
        if(!leftCard || !rightCard){
          alert('{{ __('messages.alert_images_not_found') }}');
          return;
        }
        const leftImgUrl = leftCard.querySelector('a') ? leftCard.querySelector('a').href : leftCard.querySelector('img').src;
        const rightImgUrl = rightCard.querySelector('a') ? rightCard.querySelector('a').href : rightCard.querySelector('img').src;

        // show compare area
        compareArea.style.display = 'block';

        // populate left/right with clickable full-res (open new tab)
        compareLeft.innerHTML = '<a href="'+leftImgUrl+'" target="_blank"><img src="'+leftImgUrl+'" style="max-width:100%"></a><div class="muted">{{ __('messages.image') }} '+selected[0]+'</div>';
        compareRight.innerHTML = '<a href="'+rightImgUrl+'" target="_blank"><img src="'+rightImgUrl+'" style="max-width:100%"></a><div class="muted">{{ __('messages.image') }} '+selected[1]+'</div>';

        // scroll to compare area
        compareArea.scrollIntoView({behavior:'smooth'});
      });
    })();
  </script>
@endsection
