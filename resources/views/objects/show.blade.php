@extends('layouts.app')

@section('content')
  <div class="card full">
    <h2>{{ $obj->name }} @if($obj->catalog) — {{ $obj->catalog }} @endif</h2>
    <p class="muted">{{ $obj->description }}</p>

    <div style="margin-top:12px">
      <label><strong>Show images by user:</strong></label>
      <select id="uploaderSelect">
        <option value="all">All users</option>
        @foreach($uploaders as $u)
          <option value="{{ $u->id }}">{{ $u->name ?? $u->email }}</option>
        @endforeach
      </select>

      @auth
        <label style="margin-left:20px"><strong>Your images:</strong></label>
        <select id="myImageSelect">
          <option value="">-- none --</option>
          @foreach($myImages as $mi)
            <option value="{{ $mi->id }}">My: {{ $mi->filename }} ({{ $mi->upload_time->format('Y-m-d H:i') }})</option>
          @endforeach
        </select>
      @endauth

      <button id="compareBtn" class="btn" style="margin-left:12px">Compare Selected</button>
    </div>

    <hr />

    <div id="imagesContainer" class="grid" style="margin-top:12px">
      @foreach($imagesByUser->flatten(1) as $img)
        <div class="card thumb-small image-card" data-user-id="{{ $img->user->id ?? 0 }}" data-image-id="{{ $img->id }}">
          <a href="{{ Storage::url($img->path) }}" target="_blank" title="Open full resolution">
            <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}">
          </a>
          <div style="margin-top:6px"><strong>{{ $img->filename }}</strong></div>
          <div class="muted">By: {{ $img->user->name ?? $img->user->email ?? 'guest' }}</div>
          <div class="muted">Scope: {{ $img->scopeModel->name ?? '-' }}</div>
          <div style="margin-top:6px">
            <label><input type="checkbox" class="select-compare" value="{{ $img->id }}"> select</label>
            @auth
              @if($img->user && $img->user->id == auth()->id())
                <span class="muted" style="margin-left:6px">(yours)</span>
              @endif
            @endauth
          </div>
        </div>
      @endforeach
    </div>

    <!-- Compare modal/area -->
    <div id="compareArea" style="display:none;margin-top:18px">
      <h3>Compare</h3>
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
const images = Array.from(document.querySelectorAll('.image-card[data-image-id]'));
const uploaderSelect = document.getElementById('uploaderSelect');

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
          alert('Please select two images to compare (use checkboxes).');
          return;
        }
        const leftCard = document.querySelector('.image-card[data-image-id="'+selected[0]+'"]');
        const rightCard = document.querySelector('.image-card[data-image-id="'+selected[1]+'"]');
        if(!leftCard || !rightCard){
          alert('Selected images not found.');
          return;
        }
        const leftImgUrl = leftCard.querySelector('a') ? leftCard.querySelector('a').href : leftCard.querySelector('img').src;
        const rightImgUrl = rightCard.querySelector('a') ? rightCard.querySelector('a').href : rightCard.querySelector('img').src;

        // show compare area
        compareArea.style.display = 'block';

        // populate left/right with clickable full-res (open new tab)
        compareLeft.innerHTML = '<a href="'+leftImgUrl+'" target="_blank"><img src="'+leftImgUrl+'" style="max-width:100%"></a><div class="muted">Image '+selected[0]+'</div>';
        compareRight.innerHTML = '<a href="'+rightImgUrl+'" target="_blank"><img src="'+rightImgUrl+'" style="max-width:100%"></a><div class="muted">Image '+selected[1]+'</div>';

        // scroll to compare area
        compareArea.scrollIntoView({behavior:'smooth'});
      });
    })();
  </script>
@endsection
