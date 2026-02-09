<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('members')->orderBy('name')->paginate(20);
        return view('community.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('community.groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
        ]);

        $group->members()->attach(Auth::id()); // Owner automatically joins

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully!');
    }

    public function show(Group $group)
    {
        $posts = $group->posts()->with('user', 'comments.user')->latest()->paginate(20);

        $membership = $group->members()->where('user_id', Auth::id())->first();
        $isMember = $membership && $membership->pivot->accepted_at;
        $isPending = $membership && !$membership->pivot->accepted_at;
        $isOwner = $group->owner_id === Auth::id();

        $pendingMembers = [];
        if ($isOwner) {
            $pendingMembers = $group->members()->wherePivot('accepted_at', null)->get();
        }

        return view('community.groups.show', compact('group', 'posts', 'isMember', 'isPending', 'isOwner', 'pendingMembers'));
    }

    public function join(Group $group)
    {
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            // Owner is auto-accepted, others pending
            $accepted = ($group->owner_id === Auth::id()) ? now() : null;
            $group->members()->attach(Auth::id(), ['accepted_at' => $accepted]);
        }
        return back()->with('success', 'Request sent! Waiting for approval.');
    }

    public function leave(Group $group)
    {
        $group->members()->detach(Auth::id());
        return redirect()->route('community.index')->with('success', 'Left group.');
    }

    public function approve(Group $group, \App\Models\User $user)
    {
        if ($group->owner_id !== Auth::id())
            abort(403);

        $group->members()->updateExistingPivot($user->id, ['accepted_at' => now()]);
        return back()->with('success', 'Member approved.');
    }

    public function remove(Group $group, \App\Models\User $user)
    {
        if ($group->owner_id !== Auth::id())
            abort(403);

        $group->members()->detach($user->id);
        return back()->with('success', 'Member removed.');
    }
}
