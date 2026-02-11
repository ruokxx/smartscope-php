<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('owner')->withCount('members', 'posts')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.groups.index', compact('groups'));
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return back()->with('success', 'Group deleted successfully.');
    }
}
