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

    public function verifyEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            $user->email_verified_at = null;
            $msg = 'User email marked as unverified.';
        }
        else {
            $user->markEmailAsVerified();
            $msg = 'User email manually verified.';
        }
        $user->save();
        return back()->with('success', $msg);
    }

    public function ban(Request $req, User $user)
    {
        $duration = $req->input('duration', 'permanent'); // permanent, 24h, 3d, 1w, 1m

        $user->banned_at = now();

        if ($duration === 'permanent') {
            $user->banned_until = null;
            $msg = 'User has been banned permanently.';
        }
        else {
            switch ($duration) {
                case '24h':
                    $until = now()->addDay();
                    break;
                case '3d':
                    $until = now()->addDays(3);
                    break;
                case '1w':
                    $until = now()->addWeek();
                    break;
                case '1m':
                    $until = now()->addMonth();
                    break;
                default:
                    $until = null; // fallback to permanent
            }
            $user->banned_until = $until;
            $msg = 'User has been banned until ' . $until->format('Y-m-d H:i');
        }

        $user->save();
        return back()->with('success', $msg);
    }

    public function unban(User $user)
    {
        $user->banned_at = null;
        $user->banned_until = null;
        $user->save();
        return back()->with('success', 'User has been unbanned.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
