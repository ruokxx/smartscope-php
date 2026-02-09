<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'comments')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.community.index', compact('posts'));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted by admin.');
    }
}
