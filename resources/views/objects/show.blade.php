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

      <!-- Filter Form (From Board/Compare) -->
      <div class="card" style="padding:16px; margin:24px 0 24px 0; background:rgba(255,255,255,0.02);">
          <form method="GET" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start;">
              <div style="display:flex; flex-direction:column;">
                  <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">{{ __('messages.min_exposure') }}</label>
                  <input type="number" name="min_exposure" value="{{ request('min_exposure') }}" placeholder="{{ __('messages.show_all') }}" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
              </div>
              
              <div style="display:flex; flex-direction:column;">
                  <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">{{ __('messages.filter') }}</label>
                  <select name="filter" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
                      <option value="" style="color:#000;">{{ __('messages.show_all') }}</option>
                      @foreach($filters as $f)
                          <option value="{{ $f }}" {{ request('filter') == $f ? 'selected' : '' }} style="color:#000;">{{ $f }}</option>
                      @endforeach
                  </select>
              </div>

              <div style="display:flex; flex-direction:column;">
                  <label style="display:block; font-size:12px; margin-bottom:4px; color:var(--muted); height:15px; line-height:15px; overflow:hidden; white-space:nowrap;">{{ __('messages.gain') }}</label>
                  <input type="number" name="gain" value="{{ request('gain') }}" placeholder="{{ __('messages.show_all') }}" style="margin:0; display:block; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 8px; border-radius:4px; width:120px; height:36px; box-sizing:border-box;">
              </div>

              <div style="display:flex; flex-direction:column;">
                  <label style="display:block; font-size:12px; margin-bottom:4px; color:transparent; height:15px; line-height:15px; overflow:hidden; white-space:nowrap; user-select:none;">Action</label>
                  <div style="display:flex; gap:8px;">
                      <button type="submit" class="btn" style="margin:0; display:block; background:var(--accent); color:#fff; padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid transparent; box-sizing:border-box;">{{ __('messages.apply_filters') }}</button>
                      <button type="button" id="compareBtn" class="btn" style="margin:0; display:block; background:#2ecc71; color:#fff; padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid transparent; box-sizing:border-box;">{{ __('messages.compare_selected') }}</button>
                      @if(request()->anyFilled(['min_exposure', 'filter', 'gain']))
                          <a href="{{ route('objects.show', $obj->id) }}" class="btn" style="margin:0; display:block; background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.1); padding:0 16px; height:36px; display:flex; align-items:center; justify-content:center; text-decoration:none; box-sizing:border-box;">{{ __('messages.reset') }}</a>
                      @endif
                  </div>
              </div>
          </form>
      </div>

    <!-- Comparison Columns -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:start;">
        <!-- Dwarf Column -->
        <div class="card" style="padding:12px; height:100%; display:flex; flex-direction:column;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px; margin-bottom:12px;">
                <h3 style="margin:0;">Dwarf</h3>
                <form id="dwarfFilterForm" method="GET" style="margin:0;">
                     <!-- Preserve main filters -->
                     @foreach(['min_exposure', 'filter', 'gain', 'seestar_scope_id'] as $key)
                        @if(request()->filled($key))
                            <input type="hidden" name="{{ $key }}" value="{{ request($key) }}">
                        @endif
                     @endforeach
                     <select name="dwarf_scope_id" onchange="this.form.submit()" style="background:#222; border:1px solid rgba(255,255,255,0.2); color:#fff; padding:4px 8px; border-radius:4px; font-size:12px;">
                        <option value="" style="background:#222; color:#fff;">{{ __('messages.all_models') }}</option>
                        @foreach($dwarfScopes as $scope)
                            <option value="{{ $scope->id }}" {{ request('dwarf_scope_id') == $scope->id ? 'selected' : '' }} style="background:#222; color:#fff;">{{ $scope->name }}</option>
                        @endforeach
                     </select>
                </form>
            </div>

            @if($dwarfImages->count() > 0)
                <!-- Featured (Top) Image Container -->
                <!-- We give it an ID to target it easily for updates -->
                @php $mainDwarf = $dwarfImages->first(); @endphp
                <div id="dwarf-featured" class="card thumb-small image-card featured-card" 
                     data-user-id="{{ $mainDwarf->user_id }}" 
                     data-image-id="{{ $mainDwarf->id }}" 
                     style="margin:0 auto 16px auto; padding:0; overflow:hidden; border-radius:8px; border:1px solid var(--accent); width:100%;">
                    
                    <div style="position:absolute; top:4px; left:4px; font-size:10px; color:#fff; background:var(--accent); padding:2px 4px; border-radius:4px; z-index:10;">{{ __('messages.selected') }}</div>
                    
                    <a href="{{ $mainDwarf->url }}" target="_blank" class="featured-link">
                        <img class="thumb featured-img" src="{{ $mainDwarf->url }}" style="width:100%; display:block;">
                    </a>
                    <div style="padding:8px; text-align:left; font-size:12px; background:rgba(0,0,0,0.4);">
                        <div style="margin-bottom:2px;"><strong class="featured-user" style="color:#fff;">{{ $mainDwarf->user->name ?? __('messages.user_label') }}</strong></div>
                        <div class="muted featured-meta-top">
                             {{ $mainDwarf->scopeModel->name ?? '-' }} • {{ $mainDwarf->session_date ? $mainDwarf->session_date->format('d.m.Y') : '' }}
                        </div>
                        <!-- Extended Metadata Grid -->
                        <div class="featured-stats" style="margin-top:4px; font-size:11px; display:grid; grid-template-columns:1fr 1fr; gap:4px; color:var(--text);">
                             <div>{{ __('messages.exposure_short') }}: <span class="f-exp">{{ $mainDwarf->exposure_total_seconds ? floor($mainDwarf->exposure_total_seconds/60).'m' : '-' }}</span></div>
                             <div>{{ __('messages.gain_short') }}: <span class="f-gain">{{ $mainDwarf->gain ?? '-' }}</span></div>
                             <div>{{ __('messages.filter_short') }}: <span class="f-filter">{{ $mainDwarf->filter ?? '-' }}</span></div>
                             <div>{{ __('messages.bortle_short') }}: <span class="f-bortle">{{ $mainDwarf->bortle ?? '-' }}</span></div>
                             <div>{{ __('messages.seeing_short') }}: <span class="f-seeing">{{ $mainDwarf->seeing ?? '-' }}</span></div>
                        </div>
                        <div style="margin-top:8px;">
                            <label style="cursor:pointer;">
                                {{-- We keep this checkbox for the logic, but the user interacts with the list mostly --}}
                                <input type="checkbox" class="select-compare featured-check" value="{{ $mainDwarf->id }}" {{ $dwarfImages->count() == 1 ? 'checked' : '' }}> 
                                {{ __('messages.compare') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Other Images List -->
                @if($dwarfImages->count() > 1)
                    <div style="text-align:left; font-size:12px; margin-bottom:8px; color:var(--muted);">{{ __('messages.more_results') }} ({{ $dwarfImages->count() - 1 }}):</div>
                    <div style="display:flex; flex-direction:column; gap:8px; max-height:400px; overflow-y:auto; padding-right:4px;">
                        @foreach($dwarfImages->slice(1) as $img)
                            <div class="image-card list-item" 
                                 data-user-id="{{ $img->user_id }}" 
                                 data-image-id="{{ $img->id }}"
                                 data-full-url="{{ $img->url }}"
                                 data-user-name="{{ $img->user->name ?? __('messages.user_label') }}"
                                 data-scope="{{ $img->scopeModel->name ?? '-' }}"
                                 data-date="{{ $img->session_date ? $img->session_date->format('d.m.Y') : '' }}"
                                 data-exp="{{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }}"
                                 data-gain="{{ $img->gain ?? '-' }}"
                                 data-filter="{{ $img->filter ?? '-' }}"
                                 data-bortle="{{ $img->bortle ?? '-' }}"
                                 data-seeing="{{ $img->seeing ?? '-' }}"
                                 style="display:flex; gap:8px; background:rgba(255,255,255,0.05); padding:6px; border-radius:4px;">
                                
                                <div style="width:70px; height:70px; flex-shrink:0; border-radius:4px; overflow:hidden;">
                                    <img src="{{ $img->url }}" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div style="flex:1; overflow:hidden; display:flex; flex-direction:column; justify-content:center;">
                                    <div style="display:flex; justify-content:space-between;">
                                        <span style="font-weight:600; font-size:12px;">{{ $img->user->name ?? __('messages.user_label') }}</span>
                                        <span style="font-size:10px; color:var(--muted);">{{ $img->session_date ? $img->session_date->format('d.m.y') : '' }}</span>
                                    </div>
                                    <div style="font-size:11px; color:var(--muted); margin-bottom:2px;">{{ $img->scopeModel->name ?? '-' }}</div>
                                    
                                    <div style="font-size:10px; color:var(--text); line-height:1.4;">
                                        {{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }} • G:{{ $img->gain ?? '-' }} • {{ $img->filter ?? '-' }}
                                        <br>
                                        B:{{ $img->bortle ?? '-' }} • S:{{ $img->seeing ?? '-' }}
                                    </div>
                                    
                                    <label style="font-size:11px; margin-top:4px; display:block; cursor:pointer;">
                                        <input type="checkbox" class="select-compare list-check" value="{{ $img->id }}"> {{ __('messages.select') }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="muted" style="padding:20px; text-align:center;">{{ __('messages.no_results') }}</div>
            @endif
        </div>

        <!-- Seestar Column -->
        <div class="card" style="padding:12px; height:100%; display:flex; flex-direction:column;">
             <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px; margin-bottom:12px;">
                <h3 style="margin:0;">Seestar</h3>
                <form id="seestarFilterForm" method="GET" style="margin:0;">
                     <!-- Preserve main filters -->
                     @foreach(['min_exposure', 'filter', 'gain', 'dwarf_scope_id'] as $key)
                        @if(request()->filled($key))
                            <input type="hidden" name="{{ $key }}" value="{{ request($key) }}">
                        @endif
                     @endforeach
                     <select name="seestar_scope_id" onchange="this.form.submit()" style="background:#222; border:1px solid rgba(255,255,255,0.2); color:#fff; padding:4px 8px; border-radius:4px; font-size:12px;">
                        <option value="" style="background:#222; color:#fff;">{{ __('messages.all_models') }}</option>
                        @foreach($seestarScopes as $scope)
                            <option value="{{ $scope->id }}" {{ request('seestar_scope_id') == $scope->id ? 'selected' : '' }} style="background:#222; color:#fff;">{{ $scope->name }}</option>
                        @endforeach
                     </select>
                </form>
            </div>

            @if($seestarImages->count() > 0)
                <!-- Featured (Top) Image Container -->
                @php $mainSeestar = $seestarImages->first(); @endphp
                <div id="seestar-featured" class="card thumb-small image-card featured-card"
                     data-user-id="{{ $mainSeestar->user_id }}" 
                     data-image-id="{{ $mainSeestar->id }}" 
                     style="margin:0 auto 16px auto; padding:0; overflow:hidden; border-radius:8px; border:1px solid var(--accent); width:100%;">
                    
                    <div style="position:absolute; top:4px; left:4px; font-size:10px; color:#fff; background:var(--accent); padding:2px 4px; border-radius:4px; z-index:10;">{{ __('messages.selected') }}</div>

                    <a href="{{ $mainSeestar->url }}" target="_blank" class="featured-link">
                        <img class="thumb featured-img" src="{{ $mainSeestar->url }}" style="width:100%; display:block;">
                    </a>
                    <div style="padding:8px; text-align:left; font-size:12px; background:rgba(0,0,0,0.4);">
                        <div style="margin-bottom:2px;"><strong class="featured-user" style="color:#fff;">{{ $mainSeestar->user->name ?? __('messages.user_label') }}</strong></div>
                        <div class="muted featured-meta-top">
                             {{ $mainSeestar->scopeModel->name ?? '-' }} • {{ $mainSeestar->session_date ? $mainSeestar->session_date->format('d.m.Y') : '' }}
                        </div>
                        <div class="featured-stats" style="margin-top:4px; font-size:11px; display:grid; grid-template-columns:1fr 1fr; gap:4px; color:var(--text);">
                             <div>{{ __('messages.exposure_short') }}: <span class="f-exp">{{ $mainSeestar->exposure_total_seconds ? floor($mainSeestar->exposure_total_seconds/60).'m' : '-' }}</span></div>
                             <div>{{ __('messages.gain_short') }}: <span class="f-gain">{{ $mainSeestar->gain ?? '-' }}</span></div>
                             <div>{{ __('messages.filter_short') }}: <span class="f-filter">{{ $mainSeestar->filter ?? '-' }}</span></div>
                             <div>{{ __('messages.bortle_short') }}: <span class="f-bortle">{{ $mainSeestar->bortle ?? '-' }}</span></div>
                             <div>{{ __('messages.seeing_short') }}: <span class="f-seeing">{{ $mainSeestar->seeing ?? '-' }}</span></div>
                        </div>
                        <div style="margin-top:8px;">
                            <label style="cursor:pointer;">
                                <input type="checkbox" class="select-compare featured-check" value="{{ $mainSeestar->id }}" {{ $seestarImages->count() == 1 ? 'checked' : '' }}> 
                                {{ __('messages.compare') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Other Images List -->
                 @if($seestarImages->count() > 1)
                    <div style="text-align:left; font-size:12px; margin-bottom:8px; color:var(--muted);">{{ __('messages.more_results') }} ({{ $seestarImages->count() - 1 }}):</div>
                    <div style="display:flex; flex-direction:column; gap:8px; max-height:400px; overflow-y:auto; padding-right:4px;">
                        @foreach($seestarImages->slice(1) as $img)
                            <div class="image-card list-item" 
                                 data-user-id="{{ $img->user_id }}" 
                                 data-image-id="{{ $img->id }}"
                                 data-full-url="{{ $img->url }}"
                                 data-user-name="{{ $img->user->name ?? __('messages.user_label') }}"
                                 data-scope="{{ $img->scopeModel->name ?? '-' }}"
                                 data-date="{{ $img->session_date ? $img->session_date->format('d.m.Y') : '' }}"
                                 data-exp="{{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }}"
                                 data-gain="{{ $img->gain ?? '-' }}"
                                 data-filter="{{ $img->filter ?? '-' }}"
                                 data-bortle="{{ $img->bortle ?? '-' }}"
                                 data-seeing="{{ $img->seeing ?? '-' }}"
                                 style="display:flex; gap:8px; background:rgba(255,255,255,0.05); padding:6px; border-radius:4px;">
                                
                                <div style="width:70px; height:70px; flex-shrink:0; border-radius:4px; overflow:hidden;">
                                    <img src="{{ $img->url }}" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div style="flex:1; overflow:hidden; display:flex; flex-direction:column; justify-content:center;">
                                    <div style="display:flex; justify-content:space-between;">
                                        <span style="font-weight:600; font-size:12px;">{{ $img->user->name ?? __('messages.user_label') }}</span>
                                        <span style="font-size:10px; color:var(--muted);">{{ $img->session_date ? $img->session_date->format('d.m.y') : '' }}</span>
                                    </div>
                                    <div style="font-size:11px; color:var(--muted); margin-bottom:2px;">{{ $img->scopeModel->name ?? '-' }}</div>
                                    
                                    <div style="font-size:10px; color:var(--text); line-height:1.4;">
                                        {{ $img->exposure_total_seconds ? floor($img->exposure_total_seconds/60).'m' : '-' }} • G:{{ $img->gain ?? '-' }} • {{ $img->filter ?? '-' }}
                                        <br>
                                        B:{{ $img->bortle ?? '-' }} • S:{{ $img->seeing ?? '-' }}
                                    </div>
                                    
                                    <label style="font-size:11px; margin-top:4px; display:block; cursor:pointer;">
                                        <input type="checkbox" class="select-compare list-check" value="{{ $img->id }}"> {{ __('messages.select') }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="muted" style="padding:20px; text-align:center;">{{ __('messages.no_results') }}</div>
            @endif
        </div>
    </div>
    
    <!-- Upload Button Area (Optional - keeping the 'Action' feel) -->
    @auth
        <div style="margin-top:24px; text-align:center;">
            <a href="{{ route('images.create') }}?object_id={{ $obj->id }}" class="btn" style="background:#3498db; color:#fff; display:inline-block; padding:8px 24px; text-decoration:none;">{{ __('messages.upload_image') }}</a>
        </div>
    @endauth

    <!-- Compare Modal -->
    <div id="compareModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:10000; flex-direction:column;">
        <div style="padding:16px; display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05);">
            <h3 style="margin:0; color:#fff;">{{ __('messages.compare') }}</h3>
            <button onclick="closeCompareModal()" style="background:#e74c3c; color:#fff; padding:6px 12px; border-radius:4px; font-weight:600; font-size:14px; border:none; cursor:pointer;">{{ __('messages.close') }}</button>
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
            {{ __('messages.zoom_pan_hint') }}
        </div>
    </div>

  </div>

  <script>
    (function(){
      const txtCompareSelected = "{{ __('messages.compare_selected') }}";
      const txtSelectTwo = "{{ __('messages.select_two_images') }}";

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

       // Checkbox Logic: Radio-like behavior per column + Update Featured Card
       const checkboxes = document.querySelectorAll('.select-compare');
       checkboxes.forEach(cb => {
           cb.addEventListener('change', function() {
                // Determine column container
                const colCard = this.closest('.card');
                if(!colCard) return;

                // 1. Radio-like behavior: Uncheck others in same column
                if(this.checked) {
                    const colCheckboxes = colCard.querySelectorAll('.select-compare');
                    colCheckboxes.forEach(other => {
                        if(other !== this) other.checked = false;
                    });
                    
                    // 2. Update Featured Card if this is a list item
                    // Find the featured card in this column
                    // We assume the structure: Column -> Featured Card (first .image-card)
                    // But we added IDs: dwarf-featured and seestar-featured
                    // Let's find the specific featured card for this column
                    let featured = colCard.querySelector('.featured-card');
                    
                    // If the clicked item IS the featured card's checkbox, we don't need to update content
                    // If it is a list-item, we update the featured card
                    const listItem = this.closest('.list-item');
                    if(featured && listItem) {
                        // Extract data from list item
                        const data = {
                            id: listItem.getAttribute('data-image-id'),
                            url: listItem.getAttribute('data-full-url'),
                            user: listItem.getAttribute('data-user-name'),
                            scope: listItem.getAttribute('data-scope'),
                            date: listItem.getAttribute('data-date'),
                            exp: listItem.getAttribute('data-exp'),
                            gain: listItem.getAttribute('data-gain'),
                            filter: listItem.getAttribute('data-filter'),
                            bortle: listItem.getAttribute('data-bortle'),
                            seeing: listItem.getAttribute('data-seeing'),
                        };

                        // Update Featured Card DOM
                        const img = featured.querySelector('.featured-img');
                        const link = featured.querySelector('.featured-link');
                        const user = featured.querySelector('.featured-user');
                        const meta = featured.querySelector('.featured-meta-top');
                        const check = featured.querySelector('.featured-check');
                        
                        if(img) img.src = data.url;
                        if(link) link.href = data.url; // Update link to full image
                        if(user) user.innerText = data.user;
                        if(meta) meta.innerText = data.scope + ' • ' + data.date;
                        
                        // Update Stats
                        featured.querySelector('.f-exp').innerText = data.exp;
                        featured.querySelector('.f-gain').innerText = data.gain;
                        featured.querySelector('.f-filter').innerText = data.filter;
                        featured.querySelector('.f-bortle').innerText = data.bortle;
                        featured.querySelector('.f-seeing').innerText = data.seeing;
                        
                        // Update Featured Checkbox (to allow comparison of THIS image)
                        // Actually, if we just checked the list item, that is the selected one.
                        // But the "Compare" button looks for .select-compare:checked
                        // If we check the list item, it IS checked.
                        // Any visual update to the top card is just for PREVIEW.
                        // However, if the user then clicks "Compare", it uses the checked boxes.
                        // So we don't need to change the top card's checkbox value, 
                        // UNLESS we want the top card to "become" the selected item logic-wise.
                        // But for now, we just update the visual preview.
                        // PRO STYLE: If the top card visually represents the selection, 
                        // maybe we should ensure the top card's checkbox is the one checked?
                        // No, simpler: Just keep the list item checked. 
                        // The top card is just a "Detail View" container now.
                        
                        // Wait, if I check a list item, I want to see it big.
                        // If I then uncheck it, should it revert?
                        // Yes, ideally. But for now "Last Selected" wins.
                    }
                }
                
                // If I uncheck a list item, do I revert to the default featured?
                // That would require storing the default state. 
                // Let's keep it simple: Checking an item updates the view. Unchecking leaves it (or we could revert).
                // Given the request "lass es nach oben rutschen", it implies "Select -> Promote".
                
               updateCompareButton();
           });
       });

      function updateCompareButton() {
          const selected = document.querySelectorAll('.select-compare:checked');
          const btn = document.getElementById('compareBtn');
          if(selected.length === 2) {
              btn.style.opacity = '1';
              btn.style.pointerEvents = 'auto';
              btn.innerText = txtCompareSelected + ' (' + selected.length + ')';
          } else {
              btn.style.opacity = '0.5';
              btn.style.pointerEvents = 'none';
              btn.innerText = txtSelectTwo;
          }
      }
      
      // Initialize button state
      updateCompareButton();

      // Open Modal
      document.getElementById('compareBtn').addEventListener('click', () => {
          // Logic remains same: find the checked inputs
          const selected = document.querySelectorAll('.select-compare:checked');
          if(selected.length !== 2) return;
          
          const items = [];
          selected.forEach(cb => {
              // It could be a featured card OR a list item
              const card = cb.closest('.image-card');
              items.push(card);
          });
          
          const img1 = items[0];
          const img2 = items[1];
          
          // Helper to get SRC. If it's featured, it has .featured-img, else just img
          const getSrc = (el) => {
              const img = el.querySelector('img');
              // If list item, use full url from data if available, else src
              // We added data-full-url to list items!
              if(el.dataset.fullUrl) return el.dataset.fullUrl;
              // Featured might have been updated visually but its data attributes might be stale?
              // Actually, we only updated the DOM visual elements, not the data-attributes of the referenced featured card container.
              // BUT, if the user selected a LIST item, that LIST item is the one in `selected` array.
              // So we are grabbing data from the LIST item directly.
              // Logic: `selected` contains the checkbox elements.
              // `img1` contains the closest .image-card (which is the list item).
              // So `img1.dataset.fullUrl` should work!
              return img.src; 
          };
          
          const src1 = getSrc(img1);
          const src2 = getSrc(img2);
          
          // Helper to get name
          const getName = (el) => {
             // If list item, use data attribute
             if(el.dataset.userName) return el.dataset.userName;
             return el.querySelector('strong') ? el.querySelector('strong').innerText : 'User';
          };

          document.getElementById('imgLeft').src = src1;
          document.getElementById('labelLeft').innerText = getName(img1);
          
          document.getElementById('imgRight').src = src2;
          document.getElementById('labelRight').innerText = getName(img2);
          
          modal.style.display = 'flex';
          resetState();
          document.body.style.overflow = 'hidden';
      });

    })();
  </script>
@endsection
```
