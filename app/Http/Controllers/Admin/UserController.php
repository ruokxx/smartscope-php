<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->get('q');
        $users = User::when($q, fn($qB) => $qB->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"))
            ->orderBy('id', 'desc')->paginate(25);
        return view('admin.users.index', compact('users', 'q'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $req, User $user)
    {
        $data = $req->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'nullable|in:0,1',
            'is_moderator' => 'nullable|in:0,1',
        ]);

        $data['is_admin'] = $req->has('is_admin') ? 1 : 0;
        $data['is_moderator'] = $req->has('is_moderator') ? 1 : 0;

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function toggleModerator(User $user)
    {
        $user->is_moderator = !$user->is_moderator;
        $user->save();
        return back()->with('success', 'User moderator status updated.');
    }

    public function ban(User $user)
    {
        $user->banned_at = now();
        $user->save();
        return back()->with('success', 'User has been banned.');
    }

    public function unban(User $user)
    {
        $user->banned_at = null;
        $user->save();
        return back()->with('success', 'User has been unbanned.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
