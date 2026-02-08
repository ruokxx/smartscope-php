<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(Request $req)
    {
        $user = Auth::user();

        // fetch all objects to show progress, paginated
        // we use 'page' for objects pagination
        $objects = \App\Models\Obj::orderBy('name')->paginate(12);

        // fetch user's images keyed by object_id to quickly check ownership
        // We only need one image per object to show "collected" status, preferably the latest
        $ownedImages = \App\Models\Image::where('user_id', $user->id)
            ->orderBy('upload_time', 'desc')
            ->get()
            ->keyBy('object_id'); // keyBy will overwrite duplicates, effectively keeping one (though order depends on retrieval)

        // all scopes for selection
        $allScopes = \App\Models\Scope::orderBy('name')->get();

        // fetch other users to display in sidebar (right)
        // Search functionality
        $uq = $req->get('user_q');
        $otherUsersQuery = \App\Models\User::where('id', '!=', $user->id)
            ->with('scopes')
            ->orderBy('name');

        if ($uq) {
            $otherUsersQuery->where(function ($q) use ($uq) {
                $q->where('name', 'like', "%{$uq}%")
                    ->orWhere('display_name', 'like', "%{$uq}%");
            });
        }

        // paginate users (10 per page), utilize a custom page name 'users_page'
        $otherUsers = $otherUsersQuery->paginate(10, ['*'], 'users_page')->withQueryString();

        return view('profile.edit', compact('user', 'objects', 'ownedImages', 'allScopes', 'otherUsers', 'uq'));
    }

    public function update(Request $req)
    {
        $user = Auth::user();
        $data = $req->only(['username', 'display_name', 'full_name', 'twitter', 'instagram', 'homepage']);
        $user->fill($data);
        $user->save();

        // sync scopes
        $user->scopes()->sync($req->input('scopes', []));

        return redirect()->route('profile.edit')->with('success', 'Profile saved');
    }

    public function show($id)
    {
        $user = \App\Models\User::with('scopes')->findOrFail($id);

        // Check if visitor is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // fetch all objects to show progress
        $objects = \App\Models\Obj::orderBy('name')->get();

        // fetch user's images
        $ownedImages = \App\Models\Image::where('user_id', $user->id)
            ->orderBy('upload_time', 'desc')
            ->get()
            ->keyBy('object_id');

        return view('profile.show', compact('user', 'objects', 'ownedImages'));
    }
}
