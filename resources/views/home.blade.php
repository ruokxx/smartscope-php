@extends('layouts.app')

@section('content')
  <h2 class="page-title">Latest uploads (last 24 hours)</h2>

  <div class="home-centered-container">

    <!-- horizontal scroll area: shows up to 5 visible cards, left-aligned within the centered container -->
    <div class="home-thumbs-row">
      <div style="display:inline-flex; gap:16px; align-items:flex-start;">
        @forelse($images as $img)
          <div class="card thumb-small" style="display:inline-block; vertical-align:top; width:180px; padding:8px; text-align:left;">
            <div style="height:110px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
              <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}" style="max-height:100%; max-width:100%; object-fit:cover;">
            </div>
            <div style="margin-top:8px; font-size:13px;">
              <div style="font-weight:600;">{{ \Illuminate\Support\Str::limit($img->filename, 24) }}</div>
              <div class="muted" style="font-size:12px;">{{ $img->user->name ?? '—' }}</div>
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
          <h3 style="margin-top:0; text-align:left">News</h3>
          <div id="newsList">
            @forelse($news->take(3) as $n)
              <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.03);">
                <div style="font-weight:700">{{ $n->title }}</div>
                <div class="muted" style="font-size:12px">{{ $n->created_at->format('Y-m-d') }}</div>
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
            return `<div style="display:flex;gap:8px;margin-bottom:10px;align-items:center;">
              <div style="width:120px;height:90px;overflow:hidden;border-radius:6px;background:#0f1724">
                ${url ? `<img src="${url}" style="width:100%;height:100%;object-fit:cover">` : ''}
              </div>
              <div>
                <div style="font-weight:600">${i.filename}</div>
                <div class="muted" style="font-size:12px">${new Date(i.upload_time).toLocaleString()}</div>
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
