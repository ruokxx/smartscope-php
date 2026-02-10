<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obj;
use Illuminate\Http\Request;

class DeepSkyObjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Obj::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('catalog', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $objects = $query->orderBy('catalog', 'asc')->paginate(20);

        return view('admin.objects.index', compact('objects'));
    }

    public function create()
    {
        return view('admin.objects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catalog' => 'required|string|max:255|unique:objects,catalog',
            'ra' => 'nullable|string|max:50',
            'dec' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Obj::create($validated);

        return redirect()->route('admin.objects.index')->with('success', 'Object created successfully.');
    }

    public function edit(Obj $object)
    {
        return view('admin.objects.edit', compact('object'));
    }

    public function update(Request $request, Obj $object)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catalog' => 'required|string|max:255|unique:objects,catalog,' . $object->id,
            'ra' => 'nullable|string|max:50',
            'dec' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $object->update($validated);

        return redirect()->route('admin.objects.index')->with('success', 'Object updated successfully.');
    }

    public function destroy(Obj $object)
    {
        $object->delete();
        return redirect()->route('admin.objects.index')->with('success', 'Object deleted successfully.');
    }
}
