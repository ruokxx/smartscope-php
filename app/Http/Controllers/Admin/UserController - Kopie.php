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
        $users = User::when($q, fn($qB) => $qB->where('email','like',"%{$q}%")->orWhere('name','like',"%{$q}%"))
            ->orderBy('id','desc')->paginate(25);
        return view('admin.users.index', compact('users','q'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $req, User $user)
    {
        $data = $req->validate([
            'name'=>'nullable|string|max:255',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'is_admin'=>'sometimes|boolean'
        ]);
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success','User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted');
    }
}
