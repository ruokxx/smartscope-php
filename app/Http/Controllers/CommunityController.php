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

    // Group Management
    public function createGroup()
    {
        return view('community.groups.create');
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => auth()->id(),
        ]);

        // Add owner as approved member
        $group->members()->attach(auth()->id(), ['status' => 'approved']);

        return redirect()->route('community.groups.show', $group)->with('success', 'Group created!');
    }

    public function showGroup(Group $group)
    {
        $group->load(['members', 'posts.user', 'posts.comments.user']);

        $isMember = $group->members->contains(auth()->id());
        $isPending = $group->allMembers()->where('users.id', auth()->id())->where('group_user.status', 'pending')->exists();
        $isOwner = $group->owner_id === auth()->id();

        $pendingMembers = $isOwner ? $group->pendingMembers : collect();

        return view('community.groups.show', compact('group', 'isMember', 'isPending', 'isOwner', 'pendingMembers'));
    }

    public function joinGroup(Group $group)
    {
        if ($group->allMembers->contains(auth()->id())) {
            return back()->with('error', 'Already a member or request pending.');
        }

        // If owner, auto-approve (should be covered by create logic, but just in case)
        $status = ($group->owner_id === auth()->id()) ? 'approved' : 'pending';

        $group->allMembers()->attach(auth()->id(), ['status' => $status]);

        return back()->with('success', 'Join request sent.');
    }

    public function leaveGroup(Group $group)
    {
        if ($group->owner_id === auth()->id()) {
            return back()->with('error', 'Owners cannot leave their own group.');
        }

        $group->allMembers()->detach(auth()->id());
        return redirect()->route('community.index')->with('success', 'Left group.');
    }

    public function approveRequest(Group $group, User $user)
    {
        if ($group->owner_id !== auth()->id()) {
            abort(403);
        }

        $group->allMembers()->updateExistingPivot($user->id, ['status' => 'approved']);
        return back()->with('success', 'Member approved.');
    }

    public function rejectRequest(Group $group, User $user)
    {
        if ($group->owner_id !== auth()->id()) {
            abort(403);
        }

        $group->allMembers()->detach($user->id);
        return back()->with('success', 'Request rejected.');
    }
}
