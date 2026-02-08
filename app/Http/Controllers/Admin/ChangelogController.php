<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Changelog;

class ChangelogController extends Controller
{
    public function index()
    {
        $changelogs = Changelog::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.changelogs.index', compact('changelogs'));
    }

    public function create()
    {
        return view('admin.changelogs.edit', ['changelog' => new Changelog()]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'body' => 'required|string',
            'published' => 'sometimes|boolean', // checkbox
        ]);

        if (isset($data['published']) && $data['published']) {
            $data['published_at'] = now();
        }
        else {
            $data['published_at'] = null;
        }
        unset($data['published']);

        Changelog::create($data);
        return redirect()->route('admin.changelogs.index')->with('success', 'Changelog created');
    }

    public function edit(Changelog $changelog)
    {
        return view('admin.changelogs.edit', compact('changelog'));
    }

    public function update(Request $req, Changelog $changelog)
    {
        $data = $req->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'body' => 'required|string',
            'published' => 'sometimes|boolean',
        ]);

        if (isset($data['published']) && $data['published']) {
            // retain original published_at if already set, or set to now
            $data['published_at'] = $changelog->published_at ?? now();
        }
        else {
            $data['published_at'] = null;
        }
        unset($data['published']);

        $changelog->update($data);
        return redirect()->route('admin.changelogs.index')->with('success', 'Changelog updated');
    }

    public function destroy(Changelog $changelog)
    {
        $changelog->delete();
        return redirect()->route('admin.changelogs.index')->with('success', 'Changelog deleted');
    }
}
