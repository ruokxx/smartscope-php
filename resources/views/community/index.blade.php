@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="home-centered-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
            <h2 class="page-title" style="margin-top:0; font-size:24px;">{{ __('messages.community') }}</h2>
            <a href="{{ route('community.forum.index') }}" class="btn" style="background:var(--accent); color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none;">Go to Forum</a>
        </div>
    </div>
    <div class="grid" style="display: grid; grid-template-columns: 250px 1fr 250px; gap: 24px;">
        
        <!-- Left Sidebar: Navigation & Groups -->
        <div class="sidebar-left" style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Navigation -->
            <div class="card p-4">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px; color:var(--muted);">{{ __('Community') }}</h3>
                <nav style="display:flex; flex-direction:column; gap:8px;">
                    <a href="{{ route('community.index') }}" style="display:flex; align-items:center; gap:8px; padding:8px; border-radius:8px; background:rgba(255,255,255,0.05); color:#fff; text-decoration:none;">
                        <span>🏠</span> {{ __('Home Feed') }}
                    </a>
                    <a href="{{ route('community.groups.create') }}" style="display:flex; align-items:center; gap:8px; padding:8px; border-radius:8px; background:rgba(255,255,255,0.05); color:#fff; text-decoration:none;">
                        <span>➕</span> {{ __('Create Group') }}
                    </a>
                </nav>
            </div>

            <!-- My Groups -->
            <div class="card p-4">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px; color:var(--muted);">{{ __('My Groups') }}</h3>
                @if($myGroups->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($myGroups as $group)
                            <a href="{{ route('community.groups.show', $group) }}" style="display:flex; align-items:center; gap:8px; padding:6px; border-radius:6px; color:#e6eef6; text-decoration:none; transition:background 0.2s;">
                                <div style="width:24px; height:24px; background:var(--accent); border-radius:4px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px; color:#fff;">
                                    {{ strtoupper(substr($group->name, 0, 1)) }}
                                </div>
                                <span style="font-size:14px;">{{ $group->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:13px; color:var(--muted);">{{ __('No groups joined yet.') }}</p>
                @endif
            </div>

            <!-- Suggested Groups -->
            <div class="card p-4">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px; color:var(--muted);">{{ __('Suggested') }}</h3>
                @if($allGroups->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($allGroups as $group)
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <a href="{{ route('community.groups.show', $group) }}" style="font-size:14px; color:#e6eef6; text-decoration:none;">{{ $group->name }}</a>
                                <a href="{{ route('community.groups.show', $group) }}" style="background:rgba(255,255,255,0.1); border:none; color:var(--accent); font-size:11px; padding:2px 6px; border-radius:4px; text-decoration:none;">{{ __('View') }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Center: Feed -->
        <div class="feed-center">
            <!-- Create Post -->
            <div class="card" style="margin-bottom: 24px;">
                <h2 style="margin-top:0; font-size:18px; margin-bottom:16px;">{{ __('What\'s on your mind?') }}</h2>
                <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateFileSize(this)">
                    @csrf
                    <textarea name="content" rows="3" class="input-field" placeholder="{{ __('Share something...') }}" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:8px; padding:12px; font-family:inherit; resize:vertical;"></textarea>
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
                        <div>
                            <label for="image-upload" style="cursor:pointer; color:var(--accent); font-size:14px; display:flex; align-items:center; gap:6px;">
                                📷 {{ __('Add Image') }}
                            </label>
                            <input type="file" name="image" id="image-upload" style="display:none;" accept="image/*" onchange="checkFileSize(this, 'file-name')">
                            <span id="file-name" style="font-size:12px; color:var(--muted); margin-left:8px;"></span>
                        </div>
                        <button type="submit" class="btn" style="background:var(--accent); color:#fff; padding:8px 24px;">{{ __('Post') }}</button>
                    </div>
                </form>
                <script>
                    function checkFileSize(input, spanId) {
                        if (input.files && input.files[0]) {
                            if (input.files[0].size > 1.8 * 1024 * 1024) { // 1.8MB
                                alert('File is too large. Max 1.8MB.');
                                input.value = ''; // clear input
                                document.getElementById(spanId).innerText = '';
                            } else {
                                document.getElementById(spanId).innerText = input.files[0].name;
                            }
                        }
                    }
                    function validateFileSize(form) {
                        const input = form.querySelector('input[type="file"]');
                        if (input && input.files && input.files[0]) {
                             if (input.files[0].size > 1.8 * 1024 * 1024) {
                                alert('File is too large. Max 1.8MB.');
                                return false;
                            }
                        }
                        return true;
                    }
                </script>
            </div>

            <!-- Posts List Container -->
            <div id="posts-container">
                @include('community.partials.posts_list', ['posts' => $posts])
            </div>

            <!-- Load More Button -->
            @if($posts->hasMorePages())
                <div style="margin-top:24px; text-align:center;">
                    <button id="load-more-btn" data-url="{{ $posts->nextPageUrl() }}" class="btn" style="background:rgba(255,255,255,0.05); color:var(--muted); border:1px solid rgba(255,255,255,0.1); width:100%;">
                        Start older posts
                    </button>
                    <div id="loading-spinner" style="display:none; color:var(--accent); margin-top:10px;">Loading...</div>
                </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loadMoreBtn = document.getElementById('load-more-btn');
                const container = document.getElementById('posts-container');
                const spinner = document.getElementById('loading-spinner');

                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        if (!url) return;

                        this.style.display = 'none';
                        spinner.style.display = 'block';

                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Append html
                            const temp = document.createElement('div');
                            temp.innerHTML = data.html;
                            
                            while (temp.firstChild) {
                                container.appendChild(temp.firstChild);
                            }

                            // Update button
                            if (data.next_page_url) {
                                loadMoreBtn.setAttribute('data-url', data.next_page_url);
                                loadMoreBtn.style.display = 'inline-block';
                            } else {
                                loadMoreBtn.remove(); // No more pages
                            }
                            spinner.style.display = 'none';
                        })
                        .catch(err => {
                            console.error('Error loading posts:', err);
                            spinner.style.display = 'none';
                            loadMoreBtn.style.display = 'inline-block';
                            alert('Failed to load posts.');
                        });
                    });
                }
            });
        </script>

        <!-- Right Sidebar: Online Users -->
        <div class="sidebar-right">
            <div class="card p-4">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px; color:var(--muted);">Who's Online</h3>
                @if($onlineUsers->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($onlineUsers as $user)
                            <a href="{{ route('profile.show', $user->id) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:#e6eef6;">
                                <div style="position:relative;">
                                    <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--accent), var(--accent2)); display:flex; align-items:center; justify-content:center; font-weight:bold; color:#fff; font-size:14px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div style="position:absolute; bottom:0; right:0; width:10px; height:10px; background:#2ecc71; border-radius:50%; border:2px solid var(--panel);"></div>
                                </div>
                                <span style="font-size:14px;">{{ $user->display_name ?: $user->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:13px; color:var(--muted);">No users online right now.</p>
                @endif
            </div>

            <div class="card p-4" style="margin-top:24px;">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px; color:var(--muted);">Stats</h3>
                <div style="font-size:13px; color:var(--muted);">
                    <div>Total Members: {{ \App\Models\User::count() }}</div>
                    <div>Total Groups: {{ \App\Models\Group::count() }}</div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @media (max-width: 900px) {
        .grid { grid-template-columns: 1fr !important; }
        .sidebar-left, .sidebar-right { display: none; } /* Hide sidebars on mobile, or move them */
    }
</style>
@endsection
