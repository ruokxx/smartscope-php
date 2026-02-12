<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obj;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class ObjectController extends Controller
{
    public function home()
    {
        $since = now()->subHours(24);
        $images = Image::with('user', 'object')
            ->where('approved', true) // MODERATION: Only approved images
            ->where('upload_time', '>=', $since)
            ->orderBy('upload_time', 'desc')
            ->limit(3)
            ->get();

        $news = \App\Models\News::where('published', 1)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $changelogs = \App\Models\Changelog::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // all registered users (id, name, roles)
        $users = \App\Models\User::select('id', 'name', 'is_admin', 'is_moderator', 'email')->orderBy('name')->get();

        // Community Widget (Latest 3 posts)
        $communityPosts = \App\Models\Post::with('user')->whereNull('group_id')->latest()->take(3)->get();

        // Latest Forum Threads (3)
        $latestThreads = \App\Models\ForumThread::with('user', 'category')->latest()->take(3)->get();

        return view('home', compact('images', 'news', 'changelogs', 'users', 'communityPosts', 'latestThreads'));
    }





    public function userImages(Request $req, $id)
    {
        $perPage = (int)$req->get('per_page', 3);
        $page = (int)$req->get('page', 1);

        // Show only approved images, unless user is viewing their own profile?
        // For simplicity, public profile shows approved only.
        $query = Image::with('object')->where('user_id', $id)->where('approved', true)->orderBy('upload_time', 'desc');
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // einfache JSON-Antwort mit Meta
        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }



    public function board(Request $req)
    {
        $user = Auth::user();
        $q = trim($req->get('q', ''));

        // distinct options for dropdowns
        $filters = ['Kein', 'Dualband', 'Astro'];
        // $gains removed (manual input)

        // base query for objects with optional search
        $query = Obj::query();
        if ($q !== '') {
            $query->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('catalog', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Apply Image Filters to Object Query (Show only objects that have matching images)
        if ($req->anyFilled(['min_exposure', 'filter', 'gain'])) {
            $query->whereHas('images', function ($q) use ($req, $user) {
                if ($user) {
                    $q->where('user_id', $user->id);
                }
                else {
                    $q->where('approved', true);
                }

                if ($req->filled('min_exposure')) {
                    $q->where('exposure_total_seconds', '>=', $req->min_exposure * 60);
                }
                if ($req->filled('filter')) {
                    $q->where('filter', $req->filter);
                }
                if ($req->filled('gain')) {
                    $q->where('gain', $req->gain);
                }
            });
        }

        // paginate results
        $objects = $query->orderBy('name')->paginate(25)->withQueryString();

        // preload user's latest images for visible objects
        $objectIds = $objects->pluck('id')->toArray();
        $owned = [];

        if (count($objectIds)) {
            $imgQuery = Image::whereIn('object_id', $objectIds);

            // Apply same filters to the display image fetch
            if ($req->filled('min_exposure')) {
                $imgQuery->where('exposure_total_seconds', '>=', $req->min_exposure * 60);
            }
            if ($req->filled('filter')) {
                $imgQuery->where('filter', $req->filter);
            }
            if ($req->filled('gain')) {
                $imgQuery->where('gain', $req->gain);
            }

            if ($user) {
                // User is logged in: fetch their own images
                $imgs = $imgQuery->where('user_id', $user->id)
                    ->get()
                    ->groupBy('object_id');

                foreach ($imgs as $objId => $group) {
                    $owned[$objId] = $group->sortByDesc('upload_time')->first();
                }
            }
            else {
                // Guest: fetch latest image for each visible object from ANY user
                $imgs = $imgQuery->where('approved', true) // MODERATION
                    ->orderBy('upload_time', 'desc')
                    ->get()
                    ->groupBy('object_id');

                foreach ($imgs as $objId => $group) {
                    $owned[$objId] = $group->first(); // latest because of orderBy
                }
            }
        }

        return view('board', compact('objects', 'owned', 'q', 'filters'));
    }

    // API: board with ownership info
    public function boardApi(Request $req)
    {
        $user = $req->user();
        $objects = Obj::orderBy('name')->get();
        $res = $objects->map(function ($o) use ($user) {
            $item = $o->toArray();
            $item['owned'] = false;
            $item['image'] = null;
            if ($user) {
                $img = Image::where('object_id', $o->id)->where('user_id', $user->id)->orderBy('upload_time', 'desc')->first();
                if ($img) {
                    $item['owned'] = true;
                    $item['image'] = $img;
                }
            }
            return $item;
        });
        return response()->json($res);
    }

    public function show($id)
    {
        $obj = Obj::findOrFail($id);

        // all images for this object, eager loaded
        $allImages = Image::with('user', 'scopeModel')
            ->where('object_id', $obj->id)
            ->where('approved', true) // MODERATION
            ->orderBy('upload_time', 'desc')
            ->get();

        // list distinct uploaders (users)
        $uploaders = $allImages->pluck('user')->unique('id')->values();

        // images grouped by user id
        $imagesByUser = $allImages->groupBy(function ($img) {
            return $img->user ? $img->user->id : 0;
        });

        // current user's own images for this object
        $myImages = collect();
        if (auth()->check()) {
            $myImages = $imagesByUser->get(auth()->id(), collect());
        }

        return view('objects.show', [
            'obj' => $obj,
            'uploaders' => $uploaders,
            'imagesByUser' => $imagesByUser,
            'myImages' => $myImages,
        ]);
    }

    public function showApi($id)
    {
        $obj = Obj::with('images')->findOrFail($id);
        return response()->json($obj);
    }
}
