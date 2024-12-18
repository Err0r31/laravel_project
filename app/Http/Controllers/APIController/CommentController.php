<?php

namespace App\Http\Controllers\APIController;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewCommentNotify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Jobs\VeryLongJob;
use Spatie\FlareClient\Solutions\ReportSolution;

class CommentController extends Controller
{
    public function index() {
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $comments = Cache::remember('comments'.$page, 3000, function() {
            return Comment::with(['article', 'user'])->latest()->paginate(10);
        }); 
        // return view('comment.index', ['comments'=>$comments]);
        return response()->json($comments);
    }

    public function store(Request $request) {
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comments*[0-9]'])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }

        $request->validate([
            'title'=>'required|min:4',
            'desc'=>'required|max:256'
        ]);

        $comment = new Comment;
        $comment->title = request('title');
        $comment->desc = request('desc'); 
        $comment->article_id = request('article_id');
        $comment->user_id = Auth::id();
        if ($comment->save()) {
            VeryLongJob::dispatch($comment);
            return response()->json(1);
        }     
    }

    public function edit($id) {
        $comment = Comment::findOrFail($id);
        Gate::authorize('update_comment', $comment); 
        // return view('comment.update', ['comment'=>$comment]);
        return response()->json($comment);
    }

    public function update(Request $request, Comment $comment) {
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comments*[0-9]'])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comment_article'.$comment->article_id])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }

        Gate::authorize('update_comment', $comment); 
        $request->validate([
            'title'=>'required|min:4',
            'desc'=>'required|max:256'
        ]);
        $comment->title = request('title');
        $comment->desc = request('desc'); 
        if ($comment->save()) {
            return response()->json(1);
            // return redirect()->route('article.show', $comment->article_id)->with('status', 'Comment update success');
        } else {
            return response()->json(0);
            // return redirect()->back()->with('status', 'Update failed');
        } 
    }

    public function delete($id) {
        $comment = Comment::findOrFail($id);

        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comments*[0-9]'])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comment_article'.$comment->article_id])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }
        
        Gate::authorize('update_comment', $comment);
        if ($comment->delete()) {
            return response()->json();
            // return redirect()->route('article.show', $comment->article_id)->with('status', 'Delete success');
        }
        else return redirect()->route('article.show', $comment->article_id)->with('status', 'Delete comment failed');
    }

    public function accept(Comment $comment) {
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comments*[0-9]'])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'comment_article'.$comment->article_id])->get();
        foreach($keys as $param) {
            Cache::forget($param->key);
        }

        $users = User::where('id', '!=', $comment->user_id)->get();
        $article = Article::findOrFail($comment->article_id);
        $comment->accept = true;
        if ($comment->save()) Notification::send($users, new NewCommentNotify($article, $comment->name));
        return redirect()->route('comment.index');
    }

    public function reject(Comment $comment) {
        Cache::flush();
        $comment->accept = false;
        $comment->save();
        return redirect()->route('comment.index');
    }
}