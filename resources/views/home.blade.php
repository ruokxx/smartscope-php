@extends('layouts.app')

@section('content')
  <!-- Collapsible Changelog at Top -->
  <div style="margin-bottom:24px;">
      <details style="background:rgba(255,255,255,0.02); border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
          <summary style="padding:12px; cursor:pointer; font-weight:600; color:var(--muted); font-size:14px; display:flex; align-items:center; gap:8px;">
              <span>📋 {{ __('messages.changelog') }}</span>
              <span style="font-size:11px; opacity:0.7;">(Click to expand)</span>
          </summary>
          <div style="padding:16px; border-top:1px solid rgba(255,255,255,0.05);">
              @forelse($changelogs as $c)
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.03);">
                  <div style="display:flex; justify-content:space-between; align-items:center;">
                      <span style="font-weight:700; font-size:14px;">{{ $c->title }}</span>
                      @if($c->version) <span class="muted" style="font-size:11px; background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px;">{{ $c->version }}</span> @endif
                  </div>
                  <div class="muted" style="font-size:11px; margin-top:2px;">
                      {{ ($c->published_at instanceof \DateTime ? $c->published_at : \Carbon\Carbon::parse($c->published_at ?? $c->created_at))->format('Y-m-d') }}
                  </div>
                  <div style="margin-top:4px;font-size:13px;color:#d0dce8;line-height:1.4">{!! nl2br(e(\Illuminate\Support\Str::limit($c->body, 400))) !!}</div>
                </div>
              @empty
                <p class="muted" style="font-size:13px;">No changes yet.</p>
              @endforelse
          </div>
      </details>
  </div>

  <h2 class="page-title">{{ __('messages.latest_uploads') }} (last 24 hours)</h2>

  <div class="home-centered-container">

    <!-- horizontal scroll area: shows up to 5 visible cards, left-aligned within the centered container -->
    <div class="home-thumbs-row">
      <div style="display:inline-flex; gap:16px; align-items:flex-start;">
        @forelse($images as $img)
          <div class="card thumb-small" style="display:inline-block; vertical-align:top; width:180px; padding:8px; text-align:left;">
            <div style="height:110px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
              @if($img->object_id)
                  <a href="{{ route('objects.show', $img->object_id) }}" title="{{ $img->object->name ?? 'View' }}">
                      <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->object->name ?? 'Image' }}" style="max-height:100%; max-width:100%; object-fit:cover;">
                  </a>
              @else
                  <img class="thumb" src="{{ Storage::url($img->path) }}" alt="Unknown Object" style="max-height:100%; max-width:100%; object-fit:cover;" title="No object assigned">
              @endif
            </div>
            <div style="margin-top:8px; font-size:13px;">
              <div style="font-weight:600; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#e6eef6;">
                  {{ $img->object->catalog ?? '' }} {{ $img->object->name ?? '' }}
              </div>
              <div class="muted" style="font-size:12px">{{ __('messages.by') }}: {{ $img->user->name ?? '—' }}</div>
              <div class="muted" style="font-size:11px;">{{ optional($img->upload_time)->format('d.m.Y H:i') ?? '' }}</div>
            </div>
          </div>
        @empty
          <div class="muted">No recent uploads.</div>
        @endforelse
      </div>
    </div>

    <!-- news box aligned and full-width inside centered container -->
    <div class="home-news-wrap">
      <aside>
        <div class="card" id="newsPanel" style="padding:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
             <h3 style="margin-top:0; text-align:left; margin-bottom:12px;">{{ __('messages.recent_news') }}</h3>
             <div id="newsNav" style="display:none; gap:8px;">
                 <button id="btnNewsPrev" class="btn" style="padding:4px 8px; font-size:12px;" disabled>◀</button>
                 <button id="btnNewsNext" class="btn" style="padding:4px 8px; font-size:12px;">▶</button>
             </div>
          </div>
          
          <div id="newsContainer">
            @forelse($news as $index => $n)
              <div class="news-item" data-index="{{ $index }}" style="display:{{ $index < 2 ? 'block' : 'none' }}; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.03);">
                <div class="user-info">
                    <span class="user-name">{{ $n->title }}</span>
                    <span class="upload-date muted" style="font-size:12px">{{ $n->created_at instanceof \DateTime || $n->created_at instanceof \Carbon\Carbon ? $n->created_at->format('M d, Y') : \Carbon\Carbon::parse($n->created_at)->format('M d, Y') }}</span>
                </div>
                <div style="margin-top:6px;font-size:14px;">{!! nl2br(e(\Illuminate\Support\Str::limit($n->body, 400))) !!}</div>
              </div>
            @empty
              <p class="muted">No news yet.</p>
            @endforelse
          </div>
        </div>


      </aside>
    </div>

    <!-- users + uploads side-by-side, left-aligned and symmetric spacing -->
    <div class="home-two-col">
      <aside>
        <div class="card" style="padding:16px;">
          <h3 style="margin-top:0">User Uploads</h3>
          <div id="usersList" style="max-height:240px; overflow:auto; padding-right:6px;">
            @foreach($users->take(5) as $u)
              <div style="padding:8px 4px; border-bottom:1px solid rgba(255,255,255,0.02);">
                <a href="#" class="user-link" data-user-id="{{ $u->id }}" style="color:var(--accent); text-decoration:none;">{{ $u->name ?? 'Unnamed' }}</a>
              </div>
            @endforeach
          </div>
          @if($users->count() > 5)
            <div style="margin-top:8px;text-align:center">
              <a href="{{ route('board') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)">View all users</a>
            </div>
          @endif
        </div>
      </aside>

      <aside>
        <div id="userImagesPanel" class="card" style="padding:16px; display:none;">
          <h3 id="userImagesTitle" style="margin-top:0">Uploads</h3>
          <div id="userImagesList" style="max-height:300px; overflow:auto;"></div>
          <div id="userImagesPager" style="margin-top:8px; display:flex; gap:8px; justify-content:center; align-items:center;">
            <button id="userPrev" class="btn" disabled>Prev</button>
            <span id="userPageInfo" class="muted" style="margin:0 8px"></span>
            <button id="userNext" class="btn" disabled>Next</button>
          </div>
        </div>
      </aside>
    </div>

  </div>

  <script>
    (function(){
      // News Carousel Logic
      const newsItems = document.querySelectorAll('.news-item');
      const btnNewsPrev = document.getElementById('btnNewsPrev');
      const btnNewsNext = document.getElementById('btnNewsNext');
      const newsNav = document.getElementById('newsNav');
      let newsIndex = 0;
      const newsPerPage = 2;
      
      if(newsItems.length > newsPerPage) {
          newsNav.style.display = 'flex';
      }

      function updateNews() {
          newsItems.forEach((item, index) => {
              if(index >= newsIndex && index < newsIndex + newsPerPage) {
                  item.style.display = 'block';
              } else {
                  item.style.display = 'none';
              }
          });
          btnNewsPrev.disabled = newsIndex === 0;
          btnNewsNext.disabled = newsIndex + newsPerPage >= newsItems.length;
      }

      btnNewsPrev?.addEventListener('click', () => {
          if(newsIndex > 0) {
              newsIndex -= newsPerPage;
              updateNews();
          }
      });

      btnNewsNext?.addEventListener('click', () => {
          if(newsIndex + newsPerPage < newsItems.length) {
              newsIndex += newsPerPage;
              updateNews();
          }
      });

      // User Images Logic
      const userLinks = document.querySelectorAll('.user-link');
      const userImagesPanel = document.getElementById('userImagesPanel');
      const userImagesList = document.getElementById('userImagesList');
      const userImagesTitle = document.getElementById('userImagesTitle');
      const userPrev = document.getElementById('userPrev');
      const userNext = document.getElementById('userNext');
      const userPageInfo = document.getElementById('userPageInfo');

      let currentUserId = null;
      let currentPage = 1;
      const perPage = 3;

      function normalizePathToStorage(path){
        if(!path) return '';
        // remove leading slashes
        path = path.replace(/^\/+/, '');
        if (path.startsWith('public/')) {
          return '/' + path.replace(/^public\//, 'storage/');
        }
        if (path.startsWith('storage/')) {
          return '/' + path;
        }
        // already "uploads/..." or other relative -> prefix with /storage/
        return '/storage/' + path;
      }

      async function loadUserImages(userId, page=1) {
        currentUserId = userId;
        currentPage = page;
        userImagesList.innerHTML = '<div class="muted">Loading…</div>';
        userImagesPanel.style.display = 'block';
        try {
          const res = await fetch(`/api/user/${userId}/images?per=${perPage}&page=${page}`);
          if (!res.ok) throw new Error('Fetch failed');
          const json = await res.json();
          const imgs = json.data || [];
          if (!imgs.length) {
            userImagesList.innerHTML = '<div class="muted">No uploads.</div>';
            userPrev.disabled = true; userNext.disabled = true; userPageInfo.textContent='';
            return;
          }
          userImagesList.innerHTML = imgs.map(i=>{
            const url = normalizePathToStorage(i.path);
            let displayName = i.filename;
            if (i.object) {
                // If object is attached, show Catalog (e.g. M31) + Name if present
                displayName = i.object.catalog || i.object.name;
                if (i.object.catalog && i.object.name) {
                    displayName = `${i.object.catalog} - ${i.object.name}`;
                }
            }
            
            return `<div style="display:flex;gap:8px;margin-bottom:10px;align-items:center;">
              <div style="width:120px;height:90px;overflow:hidden;border-radius:6px;background:#0f1724">
                ${url ? `<a href="/objects/${i.object_id}"><img src="${url}" style="width:100%;height:100%;object-fit:cover"></a>` : ''}
              </div>
              <div>
                <div style="font-weight:600">
                    ${i.object_id ? `<a href="/objects/${i.object_id}" style="color:inherit;text-decoration:none;">${displayName}</a>` : displayName}
                </div>
                <div class="muted" style="font-size:12px">
                    ${new Date(i.upload_time).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' })}
                </div>
              </div>
            </div>`;
          }).join('');

          const cur = json.current_page || 1, last = json.last_page || 1;
          userPrev.disabled = (cur <= 1);
          userNext.disabled = (cur >= last);
          userPageInfo.textContent = `Page ${cur} / ${last}`;
        } catch (err) {
          userImagesList.innerHTML = '<div class="muted">Error loading uploads.</div>';
          userPrev.disabled = true; userNext.disabled = true; userPageInfo.textContent='';
        }
      }

      userLinks.forEach(a=>{
        a.addEventListener('click', function(e){
          e.preventDefault();
          const id = this.dataset.userId;
          userImagesTitle.textContent = 'Uploads by ' + this.textContent;
          loadUserImages(id, 1);
        });
      });

      userPrev.addEventListener('click', function(){ if(currentUserId && currentPage>1) loadUserImages(currentUserId, currentPage-1); });
      userNext.addEventListener('click', function(){ if(currentUserId) loadUserImages(currentUserId, currentPage+1); });
    })();
  </script>
@endsection
