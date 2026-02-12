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
          <a href="{{ $img->url }}" target="_blank" title="{{ __('messages.open_full_res') }}" style="display:block; height:180px; overflow:hidden; border-radius:8px; background:#000;">
            <img class="thumb" src="{{ $img->url }}" alt="{{ $obj->name }}" style="width:100%; height:100%; object-fit:cover; display:block;">
          </a>
          <div style="margin-top:6px"><strong>{{ $obj->catalog ?? $obj->name }}</strong></div>
          <div class="muted">{{ __('messages.by') }}: <span style="color:{{ $img->user->role_color }}" class="{{ $img->user->is_admin ? 'user-admin' : ($img->user->is_moderator ? 'user-moderator' : '') }}">{{ $img->user->name ?? $img->user->email ?? 'guest' }}</span></div>
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
            <div><span style="color:var(--accent)">{{ __('messages.gain') }}:</span> {{ $img->gain ?? $img->iso_or_gain ?? '-' }}</div>
            <div><span style="color:var(--accent)">{{ __('messages.filter') }}:</span> {{ $img->filter ?? '-' }}</div>
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

    <!-- Compare Modal -->
    <div id="compareModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:10000; flex-direction:column;">
        <div style="padding:16px; display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05);">
            <h3 style="margin:0; color:#fff;">{{ __('messages.compare') }}</h3>
            <button onclick="closeCompareModal()" style="background:#e74c3c; color:#fff; padding:6px 12px; border-radius:4px; font-weight:600; font-size:14px; border:none; cursor:pointer;">Schließen</button>
        </div>
        <div style="flex:1; display:flex; gap:2px; overflow:hidden;">
            <!-- Left Image Container -->
            <div class="compare-pane" id="panLeft" style="flex:1; position:relative; overflow:hidden; border-right:1px solid rgba(255,255,255,0.1); background:#07121b;">
                <div class="pan-content" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; transform-origin:center center;">
                    <img id="imgLeft" src="" style="max-width:100%; max-height:100%; pointer-events:none; user-select:none;">
                </div>
                <div id="labelLeft" style="position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.7); color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; pointer-events:none;"></div>
            </div>
            <!-- Right Image Container -->
            <div class="compare-pane" id="panRight" style="flex:1; position:relative; overflow:hidden; background:#07121b;">
                <div class="pan-content" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; transform-origin:center center;">
                     <img id="imgRight" src="" style="max-width:100%; max-height:100%; pointer-events:none; user-select:none;">
                </div>
                <div id="labelRight" style="position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.7); color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; pointer-events:none;"></div>
            </div>
        </div>
        <div style="padding:8px; text-align:center; color:var(--muted); font-size:12px;">
            Scroll to Zoom • Drag to Pan
        </div>
    </div>

  </div>

  <script>
    (function(){
      const images = Array.from(document.querySelectorAll('.image-card[data-image-id]'));
      const uploaderSelect = document.getElementById('uploaderSelect');
      const myImageSelect = document.getElementById('myImageSelect');
      const compareBtn = document.getElementById('compareBtn');
      const modal = document.getElementById('compareModal');
      
      // Move modal to body to ensure it sits on top of header (z-index context fix)
      if(modal && document.body) {
          document.body.appendChild(modal);
      }
      
      // Pan/Zoom State
      let state = {
          left: { scale: 1, x: 0, y: 0, isDragging: false, startX: 0, startY: 0 },
          right: { scale: 1, x: 0, y: 0, isDragging: false, startX: 0, startY: 0 },
      };

      window.closeCompareModal = function(){
          modal.style.display = 'none';
          document.body.style.overflow = '';
      };

      function resetState(){
          state = {
              left: { scale: 1, x: 0, y: 0, isDragging: false, startX: 0, startY: 0 },
              right: { scale: 1, x: 0, y: 0, isDragging: false, startX: 0, startY: 0 },
          };
          updateTransform(document.querySelector('#panLeft .pan-content'), state.left);
          updateTransform(document.querySelector('#panRight .pan-content'), state.right);
      }

      function updateTransform(el, s) {
          el.style.transform = `translate(${s.x}px, ${s.y}px) scale(${s.scale})`;
      }

      function setupPanZoom(paneId, stateKey) {
          const pane = document.getElementById(paneId);
          const content = pane.querySelector('.pan-content');

          pane.addEventListener('wheel', (e) => {
              e.preventDefault();
              const zoomSpeed = 0.1;
              const direction = e.deltaY > 0 ? -1 : 1;
              let newScale = state[stateKey].scale + (direction * zoomSpeed * state[stateKey].scale);
              newScale = Math.max(0.5, Math.min(newScale, 10)); // Min 0.5x, Max 10x
              state[stateKey].scale = newScale;
              updateTransform(content, state[stateKey]);
          });

          pane.addEventListener('mousedown', (e) => {
              state[stateKey].isDragging = true;
              state[stateKey].startX = e.clientX - state[stateKey].x;
              state[stateKey].startY = e.clientY - state[stateKey].y;
              pane.style.cursor = 'grabbing';
          });

          window.addEventListener('mousemove', (e) => {
              if (!state[stateKey].isDragging) return;
              e.preventDefault();
              state[stateKey].x = e.clientX - state[stateKey].startX;
              state[stateKey].y = e.clientY - state[stateKey].startY;
              updateTransform(content, state[stateKey]);
          });

          window.addEventListener('mouseup', () => {
              state[stateKey].isDragging = false;
              pane.style.cursor = 'grab';
          });
      }

      setupPanZoom('panLeft', 'left');
      setupPanZoom('panRight', 'right');


      function filterByUser(userId){
        images.forEach(card => {
          const uid = card.getAttribute('data-user-id') || '0';
          if(userId === 'all' || String(uid) === String(userId)) {
            card.style.display = '';
            const img = card.querySelector('img.thumb');
            if(img && !img.src) img.src = img.dataset.src || img.getAttribute('data-src') || img.getAttribute('src');
          } else {
            card.style.display = 'none';
          }
        });
      }

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
        const selected = Array.from(document.querySelectorAll('.select-compare:checked'));
        if (selected.length < 2){
          alert('{{ __('messages.alert_select_two') }}');
          return;
        }
        
        const id1 = selected[0].value;
        const id2 = selected[1].value;

        const card1 = document.querySelector('.image-card[data-image-id="'+id1+'"]');
        const card2 = document.querySelector('.image-card[data-image-id="'+id2+'"]');

        if(!card1 || !card2) return;

        // Extract full res URL (from Anchor) or fallback to img src
        const url1 = card1.querySelector('a') ? card1.querySelector('a').href : card1.querySelector('img').src;
        const url2 = card2.querySelector('a') ? card2.querySelector('a').href : card2.querySelector('img').src;

        // Setup Modal
        document.getElementById('imgLeft').src = url1;
        document.getElementById('imgRight').src = url2;
        
        document.getElementById('labelLeft').innerText = 'Image ' + id1;
        document.getElementById('labelRight').innerText = 'Image ' + id2;

        resetState();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
      });

    })();
  </script>
@endsection
