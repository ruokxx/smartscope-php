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
          <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}">
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
      const images = Array.from(document.querySelectorAll('.image-card'));
      const uploaderSelect = document.getElementById('uploaderSelect');
      const myImageSelect = document.getElementById('myImageSelect');
      const compareBtn = document.getElementById('compareBtn');
      const compareArea = document.getElementById('compareArea');
      const compareLeft = document.getElementById('compareLeft');
      const compareRight = document.getElementById('compareRight');

      function filterByUser(userId){
        images.forEach(card => {
          const uid = card.getAttribute('data-user-id');
          if(userId === 'all' || String(uid) === String(userId)){
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      }

      uploaderSelect.addEventListener('change', function(){
        filterByUser(this.value);
      });

      if (myImageSelect){
        myImageSelect.addEventListener('change', function(){
          if (this.value){
            // show only uploader = your id AND also keep others? we'll filter to owner
            filterByUser('{{ auth()->id() ?? "" }}');
            // Also mark the selected checkbox
            images.forEach(card => {
              const iid = card.getAttribute('data-image-id');
              card.querySelector('.select-compare').checked = (iid === this.value);
            });
          } else {
            filterByUser(uploaderSelect.value);
            images.forEach(card => card.querySelector('.select-compare').checked = false);
          }
        });
      }

      compareBtn.addEventListener('click', function(){
        const selected = Array.from(document.querySelectorAll('.select-compare:checked')).map(i=>i.value);
        if (selected.length < 2){
          alert('Please select two images to compare (use checkboxes).');
          return;
        }
        // show compare area
        compareArea.style.display = 'block';
        // populate left/right
        const left = document.querySelector('.image-card[data-image-id="'+selected[0]+'"] img').src;
        const right = document.querySelector('.image-card[data-image-id="'+selected[1]+'"] img').src;
        compareLeft.innerHTML = '<img src="'+left+'" style="max-width:100%"><div class="muted">Image '+selected[0]+'</div>';
        compareRight.innerHTML = '<img src="'+right+'" style="max-width:100%"><div class="muted">Image '+selected[1]+'</div>';
        // scroll to compare area
        compareArea.scrollIntoView({behavior:'smooth'});
      });
    })();
  </script>
@endsection
