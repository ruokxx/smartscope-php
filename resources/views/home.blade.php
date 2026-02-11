@extends('layouts.app')

@section('content')
  <!-- Collapsible Changelog at Top -->
  <div style="margin-bottom:24px;">
      <details style="background:rgba(255,255,255,0.02); border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
          <summary style="padding:12px; cursor:pointer; font-weight:600; color:var(--muted); font-size:14px; display:flex; align-items:center; gap:8px;">
              <span>📋 {{ __('Changelog') }}</span>
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
                  <div style="margin-top:4px;font-size:13px;color:#d0dce8;line-height:1.4">{!! nl2br(e($c->body)) !!}</div>
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
              <div class="muted" style="font-size:12px">{{ __('messages.by') }}: <span class="{{ (optional($img->user)->is_admin || optional($img->user)->is_moderator) ? 'team-member-name' : '' }}" style="color:{{ $img->user->role_color }}">{{ $img->user->name ?? '—' }}</span></div>
              <div class="muted" style="font-size:11px;">{{ optional($img->upload_time)->format('d.m.Y H:i') ?? '' }}</div>
            </div>
          </div>
        @empty
          <div class="muted">No recent uploads.</div>
        @endforelse
      </div>
    </div>

    <!-- News Section (Full Width) -->
    <div style="margin-bottom:24px;">
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
              <div class="news-item" data-index="{{ $index }}" style="display:{{ $index < 2 ? 'block' : 'none' }}; margin-bottom:12px; padding:12px; background:rgba(255,255,255,0.03); border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
                <div class="user-info" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:8px; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
                    <span class="user-name {{ (optional($n->user)->is_admin || optional($n->user)->is_moderator) ? 'team-member-name' : '' }}" style="font-weight:bold; font-size:15px; color:{{ $n->user->role_color ?? 'var(--accent)' }}">{{ $n->title }}</span>
                    <span class="upload-date muted" style="font-size:12px; white-space:nowrap;">{{ $n->created_at instanceof \DateTime || $n->created_at instanceof \Carbon\Carbon ? $n->created_at->format('d.m.Y H:i') : \Carbon\Carbon::parse($n->created_at)->format('d.m.Y H:i') }}</span>
                </div>
                <div class="news-body">
                    {!! \Illuminate\Support\Str::markdown($n->body) !!}
                </div>
              </div>
            @empty
              <p class="muted">No news yet.</p>
            @endforelse
          </div>
        </div>
    </div>

    <!-- Forum & Community Grid -->
    <div class="home-widgets-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Forum Widget -->
        @if(\App\Models\Setting::where('key', 'forum_enabled')->value('value') !== '0')
        <div class="card" style="padding:16px; height:100%; display:flex; flex-direction:column;">
            <h3 style="margin-top:0; margin-bottom:12px;">{{ __('Latest Forum Threads') }}</h3>
             @forelse($latestThreads as $thread)
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; gap:8px; align-items:flex-start;">
                        <div style="width:32px; height:32px; border-radius:50%; background:#2c3e50; overflow:hidden; flex-shrink:0;">
                             <img src="{{ $thread->user->avatar_url }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="overflow:hidden; flex:1;">
                            <div style="font-size:14px; font-weight:bold;">
                                <a href="{{ route('community.forum.thread', $thread->id) }}" style="text-decoration:none; color:inherit;">{{ $thread->title }}</a>
                            </div>
                            <div style="font-size:11px; color:var(--muted); margin-top:2px; display:flex; gap:6px; align-items:center;">
                                <span style="background:rgba(255,255,255,0.1); padding:1px 4px; border-radius:3px;">{{ $thread->category->name ?? 'General' }}</span>
                                <span>by <span class="{{ (optional($thread->user)->is_admin || optional($thread->user)->is_moderator) ? 'team-member-name' : '' }}" style="color:{{ $thread->user->role_color }}">{{ $thread->user->display_name ?: $thread->user->name }}</span></span>
                                <span>&bull; {{ $thread->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted" style="font-size:13px;">{{ __('No recent threads.') }}</p>
            @endforelse
            <div style="margin-top:auto; text-align:right;">
                @guest
                    <a href="#" onclick="event.preventDefault(); openAuthModal('login', '{{ __('messages.login_required') }}')" style="font-size:12px; color:var(--accent);">{{ __('Go to Forum') }} &rarr;</a>
                @else
                    <a href="{{ route('community.forum.index') }}" style="font-size:12px; color:var(--accent);">{{ __('Go to Forum') }} &rarr;</a>
                @endguest
            </div>
        </div>
        @endif

        <!-- Community Widget -->
        @if(\App\Models\Setting::where('key', 'community_enabled')->value('value') !== '0')
        <div class="card" style="padding:16px; height:100%; display:flex; flex-direction:column;">
            <h3 style="margin-top:0; margin-bottom:12px;">{{ __('Recent Community Activity') }}</h3>
            @forelse($communityPosts as $post)
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; gap:8px; align-items:flex-start;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--accent); display:flex; align-items:center; justify-content:center; color:#fff; font-size:10px; flex-shrink:0;">
                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                        </div>
                        <div style="overflow:hidden; flex:1;">
                            <div class="{{ (optional($post->user)->is_admin || optional($post->user)->is_moderator) ? 'team-member-name' : '' }}" style="font-size:12px; font-weight:bold; color:{{ $post->user->role_color }};">
                                {{ $post->user->display_name ?: $post->user->name }}
                            </div>
                            <div style="font-size:13px; color:#ddd; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $post->content }}
                            </div>
                            <div style="font-size:10px; color:var(--muted); margin-top:2px;">
                                {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted" style="font-size:13px;">{{ __('No recent activity.') }}</p>
            @endforelse
            <div style="margin-top:auto; text-align:right;">
                @guest
                    <a href="#" onclick="event.preventDefault(); openAuthModal('login', '{{ __('messages.login_required') }}')" style="font-size:12px; color:var(--accent);">{{ __('View all') }} &rarr;</a>
                @else
                    <a href="{{ route('community.index') }}" style="font-size:12px; color:var(--accent);">{{ __('View all') }} &rarr;</a>
                @endguest
            </div>
        </div>
        @endif

    </div>
    
        .news-body { 
            font-size: 14px; 
            line-height: 1.6; 
            color: #d1d5db; 
            overflow-wrap: break-word; 
            word-wrap: break-word; 
            word-break: break-word; 
            hyphens: auto; 
            max-width: 100%;
        }
        .news-body ul, .news-body ol { 
            padding-left: 1.2em; 
            margin: 0.5em 0; 
        }
        .news-body li {
            margin-bottom: 0.25em;
        }
        .news-body p { 
            margin-bottom: 0.75em; 
        }
        .news-body p:last-child { 
            margin-bottom: 0; 
        }
        .news-body img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        @media (max-width: 800px) {
            .home-widgets-grid { grid-template-columns: 1fr; }
            .news-item { padding: 12px !important; }
        }
    </style>

    <!-- users + uploads side-by-side, left-aligned and symmetric spacing -->
    <div class="home-two-col">
      <aside>
        <div class="card" style="padding:16px;">
          <h3 style="margin-top:0">User Uploads</h3>
          <div id="usersList" style="max-height:240px; overflow:auto; padding-right:6px;">
            @foreach($users->take(5) as $u)
              <div style="padding:8px 4px; border-bottom:1px solid rgba(255,255,255,0.02);">
                <a href="#" class="user-link {{ (optional($u)->is_admin || optional($u)->is_moderator) ? 'team-member-name' : '' }}" data-user-id="{{ $u->id }}" style="color:{{ $u->role_color === 'inherit' ? 'var(--accent)' : $u->role_color }}; text-decoration:none;">{{ $u->name ?? 'Unnamed' }}</a>
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
      const btnPrev = document.getElementById('btnNewsPrev');
      const btnNext = document.getElementById('btnNewsNext');
      const newsNav = document.getElementById('newsNav');
      
      let newsIndex = 0;
      const newsPerPage = 2; // Max 2 news visible
      
      function updateNews() {
        newsItems.forEach((item, i) => {
            if (i >= newsIndex && i < newsIndex + newsPerPage) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        
        btnPrev.disabled = (newsIndex === 0);
        btnNext.disabled = (newsIndex + newsPerPage >= newsItems.length);
      }
      
      if (newsItems.length > newsPerPage) {
          newsNav.style.display = 'flex';
          
          btnPrev.addEventListener('click', () => {
              if (newsIndex > 0) {
                  newsIndex -= newsPerPage; // Jump by 2 (or 1?) - let's jump by 2
                  if(newsIndex < 0) newsIndex = 0;
                  updateNews();
              }
          });
          
          btnNext.addEventListener('click', () => {
              if (newsIndex + newsPerPage < newsItems.length) {
                  newsIndex += newsPerPage;
                  updateNews();
              }
          });
      }

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
