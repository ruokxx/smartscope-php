<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $req)
    {
        $user = Auth::user();
        $data = $req->only(['username','display_name','full_name','email','twitter','instagram','homepage']);
        $user->fill($data);
        $user->save();
        return redirect()->route('profile.edit')->with('success','Profile saved');
    }
}
