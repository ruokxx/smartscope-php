<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('created_at','desc')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.edit', ['news' => new News()]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title'=>'required|string|max:255',
            'body'=>'nullable|string',
            'published'=>'sometimes|boolean'
        ]);
        $data['published'] = isset($data['published']) ? 1 : 0;
        News::create($data);
        return redirect()->route('admin.news.index')->with('success','News created');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $req, News $news)
    {
        $data = $req->validate([
            'title'=>'required|string|max:255',
            'body'=>'nullable|string',
            'published'=>'sometimes|boolean'
        ]);
        $data['published'] = isset($data['published']) ? 1 : 0;
        $news->update($data);
        return redirect()->route('admin.news.index')->with('success','News updated');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success','News deleted');
    }
}
