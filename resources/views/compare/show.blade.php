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
                  <button type="button" id="compareBtn" class="btn" style="margin:0; display:block; background:#2ecc71; color:#fff; padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid transparent; box-sizing:border-box;">Compare Selected</button>
                  @if(request()->anyFilled(['min_exposure', 'filter', 'gain']))
                      <a href="{{ route('compare.show', $object->id) }}" class="btn" style="margin:0; display:block; background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.1); padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; text-decoration:none; box-sizing:border-box;">Reset</a>
                  @endif
              </div>
          </div>
      </form>
  </div>
  
  <div style="display:flex;gap:12px; align-items:flex-start;">
    <!-- Dwarf Column -->
    <div class="card" style="flex:1;text-align:center; padding:12px;">
      <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px; margin-top:0;">Dwarf</h3>
      @if($dwarfImages->count() > 0)
        <!-- Featured (First) Image -->
        @php $mainDwarf = $dwarfImages->first(); @endphp
        <div class="card thumb-small image-card" data-user-id="{{ $mainDwarf->user_id }}" data-image-id="{{ $mainDwarf->id }}" style="margin:0 auto 16px auto; padding:0; overflow:hidden; border-radius:8px; border:1px solid var(--accent);">
            <div style="position:absolute; top:4px; left:4px; font-size:10px; color:#fff; background:var(--accent); padding:2px 4px; border-radius:4px; z-index:10;">Best Match</div>
            <a href="{{ $mainDwarf->url }}" target="_blank">
                <img class="thumb" src="{{ $mainDwarf->url }}" style="width:100%; display:block;">
            </a>
             <div style="padding:8px; text-align:left; font-size:12px;">
                <div><span class="muted">Exp:</span> {{ $mainDwarf->exposure_total_seconds ? floor($mainDwarf->exposure_total_seconds/60).'m' : '-' }}</div>
                <div><span class="muted">Filter:</span> {{ $mainDwarf->filter }} | <span class="muted">Gain:</span> {{ $mainDwarf->gain }}</div>
                <div style="margin-top:4px;"><label><input type="checkbox" class="select-compare" value="{{ $mainDwarf->id }}"> Compare</label></div>
            </div>
        </div>

        <!-- Other Images List -->
        @if($dwarfImages->count() > 1)
            <div style="text-align:left; font-size:12px; margin-bottom:8px; color:var(--muted);">More results ({{ $dwarfImages->count() - 1 }}):</div>
            <div style="display:flex; flex-direction:column; gap:8px; max-height:400px; overflow-y:auto; padding-right:4px;">
                @foreach($dwarfImages->slice(1) as $img)
                    <div class="image-card" data-user-id="{{ $img->user_id }}" data-image-id="{{ $img->id }}" style="display:flex; gap:8px; background:rgba(255,255,255,0.05); padding:6px; border-radius:4px;">
                        <a href="{{ $img->url }}" target="_blank" style="width:60px; height:60px; flex-shrink:0; display:block; border-radius:4px; overflow:hidden;">
                            <img src="{{ $img->url }}" style="width:100%; height:100%; object-fit:cover;">
                        </a>
                        <div style="flex:1; overflow:hidden;">
                           <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $img->user->name ?? 'User' }}</div>
                           <div class="muted" style="font-size:11px;">
                               {{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }} 
                               @if($img->filter) • {{ $img->filter }} @endif
                               @if($img->gain) • G:{{ $img->gain }} @endif
                           </div>
                           <label style="font-size:11px; margin-top:2px; display:block; cursor:pointer;"><input type="checkbox" class="select-compare" value="{{ $img->id }}"> Select</label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

      @else
        <div class="muted" style="padding:20px;">No Dwarf images found matching filters.</div>
      @endif
    </div>

    <div style="align-self:center; font-weight:bold; color:var(--muted);">VS</div>

    <!-- Seestar Column -->
    <div class="card" style="flex:1;text-align:center; padding:12px;">
      <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px; margin-top:0;">Seestar</h3>
      @if($seestarImages->count() > 0)
        <!-- Featured (First) Image -->
        @php $mainSeestar = $seestarImages->first(); @endphp
        <div class="card thumb-small image-card" data-user-id="{{ $mainSeestar->user_id }}" data-image-id="{{ $mainSeestar->id }}" style="margin:0 auto 16px auto; padding:0; overflow:hidden; border-radius:8px; border:1px solid var(--accent);">
            <div style="position:absolute; top:4px; left:4px; font-size:10px; color:#fff; background:var(--accent); padding:2px 4px; border-radius:4px; z-index:10;">Best Match</div>
            <a href="{{ $mainSeestar->url }}" target="_blank">
                <img class="thumb" src="{{ $mainSeestar->url }}" style="width:100%; display:block;">
            </a>
            <div style="padding:8px; text-align:left; font-size:12px;">
                <div><span class="muted">Exp:</span> {{ $mainSeestar->exposure_total_seconds ? floor($mainSeestar->exposure_total_seconds/60).'m' : '-' }}</div>
                <div><span class="muted">Filter:</span> {{ $mainSeestar->filter }} | <span class="muted">Gain:</span> {{ $mainSeestar->gain }}</div>
                <div style="margin-top:4px;"><label><input type="checkbox" class="select-compare" value="{{ $mainSeestar->id }}"> Compare</label></div>
            </div>
        </div>

        <!-- Other Images List -->
        @if($seestarImages->count() > 1)
            <div style="text-align:left; font-size:12px; margin-bottom:8px; color:var(--muted);">More results ({{ $seestarImages->count() - 1 }}):</div>
            <div style="display:flex; flex-direction:column; gap:8px; max-height:400px; overflow-y:auto; padding-right:4px;">
                 @foreach($seestarImages->slice(1) as $img)
                    <div class="image-card" data-user-id="{{ $img->user_id }}" data-image-id="{{ $img->id }}" style="display:flex; gap:8px; background:rgba(255,255,255,0.05); padding:6px; border-radius:4px;">
                        <a href="{{ $img->url }}" target="_blank" style="width:60px; height:60px; flex-shrink:0; display:block; border-radius:4px; overflow:hidden;">
                            <img src="{{ $img->url }}" style="width:100%; height:100%; object-fit:cover;">
                        </a>
                        <div style="flex:1; overflow:hidden;">
                           <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $img->user->name ?? 'User' }}</div>
                           <div class="muted" style="font-size:11px;">
                               {{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }} 
                               @if($img->filter) • {{ $img->filter }} @endif
                               @if($img->gain) • G:{{ $img->gain }} @endif
                           </div>
                           <label style="font-size:11px; margin-top:2px; display:block; cursor:pointer;"><input type="checkbox" class="select-compare" value="{{ $img->id }}"> Select</label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

      @else
        <div class="muted" style="padding:20px;">No Seestar images found matching filters.</div>
      @endif
    </div>
  </div>

  <!-- Compare Modal -->
    <div id="compareModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:10000; flex-direction:column;">
        <div style="padding:16px; display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05);">
            <h3 style="margin:0; color:#fff;">{{ __('messages.compare') }}</h3>
            <button onclick="closeCompareModal()" style="background:#e74c3c; color:#fff; padding:6px 12px; border-radius:4px; font-weight:600; font-size:14px; border:none; cursor:pointer;">Close</button>
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

  <script>
    (function(){
      const compareBtn = document.getElementById('compareBtn');
      const modal = document.getElementById('compareModal');
      
      // Move modal to body
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

      if(compareBtn){
          compareBtn.addEventListener('click', function(){
            const selected = Array.from(document.querySelectorAll('.select-compare:checked'));
            if (selected.length < 2){
              alert('Please select at least two images to compare.');
              return;
            }
            
            const id1 = selected[0].value;
            const id2 = selected[1].value;

            const card1 = document.querySelector('.image-card[data-image-id="'+id1+'"]');
            const card2 = document.querySelector('.image-card[data-image-id="'+id2+'"]');

            if(!card1 || !card2) return;

            // Extract full res URL
            const url1 = card1.querySelector('a') ? card1.querySelector('a').href : card1.querySelector('img').src;
            const url2 = card2.querySelector('a') ? card2.querySelector('a').href : card2.querySelector('img').src;

            // Setup Modal
            document.getElementById('imgLeft').src = url1;
            document.getElementById('imgRight').src = url2;
            
            document.getElementById('labelLeft').innerText = 'Image ' + id1;
            document.getElementById('labelRight').innerText = 'Image ' + id2;

            resetState();
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
          });
      }

    })();
  </script>
@endsection
