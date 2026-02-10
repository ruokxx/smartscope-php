<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        // Check feature toggle
        $isEnabled = \App\Models\Setting::where('key', 'forum_enabled')->value('value');
        if ($isEnabled === '0' && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Forum is disabled.');
        }

        $categories = ForumCategory::whereNull('parent_id')->orderBy('order')->with(['children' => function ($q) {
            $q->orderBy('order');
        }, 'threads' => function ($q) {
            $q->latest()->take(3);
        }])->get();
        return view('community.forum.index', compact('categories'));
    }

    public function category(ForumCategory $category)
    {
        $threads = $category->threads()->with('user')->withCount('posts')->orderBy('is_pinned', 'desc')->latest()->paginate(20);
        return view('community.forum.category', compact('category', 'threads'));
    }

    public function thread(ForumThread $thread)
    {
        $thread->increment('view_count');
        $posts = $thread->posts()->with('user')->paginate(15);
        return view('community.forum.thread', compact('thread', 'posts'));
    }

    public function createThread(ForumCategory $category)
    {
        return view('community.forum.create', compact('category'));
    }

    public function storeThread(Request $request, ForumCategory $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $thread = ForumThread::create([
            'category_id' => $category->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
        ]);

        ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->route('community.forum.thread', $thread)->with('success', 'Thread created.');
    }

    public function storePost(Request $request, ForumThread $thread)
    {
        if ($thread->is_locked) {
            return back()->with('error', 'Thread is locked.');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $thread->touch(); // Bump updated_at

        return back()->with('success', 'Reply posted.');
    }

    public function destroyThread(ForumThread $thread)
    {
        if (auth()->id() !== $thread->user_id && !auth()->user()->isModerator()) {
            abort(403);
        }
        $thread->delete();
        return redirect()->route('community.forum.category', $thread->category)->with('success', 'Thread deleted.');
    }

    public function destroyPost(ForumPost $post)
    {
        if (auth()->id() !== $post->user_id && !auth()->user()->isModerator()) {
            abort(403);
        }
        $post->delete();
        return back()->with('success', 'Post deleted.');
    }
}
