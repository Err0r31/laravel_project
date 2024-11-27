<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\User;

use App\Providers\ArticleServiceProvider;


class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::latest()->paginate(6);
        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'date',
            'name' => 'required|min:5|max:100',
            'desc' => 'required|min:5'
        ]);

        $article = new Article;
        $article->date = $request->date;
        $article->name = $request->name;
        $article->text = $request->desc;
        $article->user_id = 1;
        $article->save();
        return redirect('/articles');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $comments = Comment::where('article_id', $article->id)->get();
        $auth = User::findOrFail($article->user_id);
        return view('articles.show', ['article' => $article, 'auth' => $auth, 'comments' => $comments]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('articles.update', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'date' => 'date',
            'name' => 'required|min:5|max:100',
            'desc' => 'required|min:5'
        ]);

        $article->date = $request->date;
        $article->name = $request->name;
        $article->text = $request->desc;
        $article->user_id = 1;
        if ($article->save()) return redirect('/articles')->with('status', 'Update success');
        else return redirect()->route('articles.index')->with('status', 'Update failed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->delete()) return redirect('/articles')->with('status', 'Delete success');
        else return redirect()->route('article.show', ['article'=>$article->id])->with('status','Delete don`t success');
    }
}
