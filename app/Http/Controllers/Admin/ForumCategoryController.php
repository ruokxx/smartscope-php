<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;

class ForumCategoryController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::orderBy('order')->get();
        $parents = ForumCategory::whereNull('parent_id')->orderBy('order')->get();
        return view('admin.forum.index', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'integer',
            'parent_id' => 'nullable|exists:forum_categories,id'
        ]);

        ForumCategory::create($request->all());
        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, ForumCategory $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'integer',
            'parent_id' => 'nullable|exists:forum_categories,id'
        ]);

        $category->update($request->all());
        return back()->with('success', 'Category updated.');
    }

    public function destroy(ForumCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
