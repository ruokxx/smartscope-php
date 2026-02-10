<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Group;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        // Check if feature is enabled (unless admin)
        $isEnabled = \App\Models\Setting::where('key', 'community_enabled')->value('value');
        if ($isEnabled === '0' && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Community is disabled.');
        }

        $posts = Post::whereNull('group_id') // Only show global posts in main feed
            ->with('user', 'comments.user', 'group')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Reduced from 20 for better "load more" UX

        if ($request->ajax()) {
            $view = view('community.partials.posts_list', compact('posts'))->render();
            return response()->json(['html' => $view, 'next_page_url' => $posts->nextPageUrl()]);
        }

        $onlineUsers = \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(5))->get();
        $myGroups = auth()->user()->groups;
        $allGroups = Group::withCount('members')->orderBy('members_count', 'desc')->take(5)->get(); // Suggested groups

        return view('community.index', compact('posts', 'onlineUsers', 'myGroups', 'allGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('community_images', 'public');
            $imagePath = $path;
        }

        Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'image_path' => $imagePath,
            'group_id' => $request->group_id ?? null,
        ]);

        return back()->with('success', 'Post created successfully.');
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Comment added.');
    } // Removed extra brace here

    public function destroy(Post $post)
    {
        // Allow author or admin/moderator to delete
        if (auth()->id() !== $post->user_id && !auth()->user()->isModerator()) {
            abort(403);
        }

        $post->delete();
        return back()->with('success', 'Post deleted.');
    }

    public function destroyComment(Comment $comment)
    {
        // Allow author or admin/moderator to delete
        if (auth()->id() !== $comment->user_id && !auth()->user()->isModerator()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
