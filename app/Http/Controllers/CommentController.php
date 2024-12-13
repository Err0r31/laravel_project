<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Jobs\VeryLongJob;


class CommentController extends Controller
{
    public function index() {
        $comments = Comment::with(['article', 'user'])->latest()->paginate(10);
        return view('comment.index', ['comments'=>$comments]);
    }

    public function store(Request $request){
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
            return redirect()->back()->with('status', 'Add new comment');
        }
        else return redirect()->back()->with('status', 'Add failed');        
    }

    public function edit($id){
        $comment = Comment::findOrFail($id);
        Gate::authorize('update_comment', $comment); 
        return view('comment.update', ['comment'=>$comment]);
    }

    public function update(Request $request, Comment $comment){
        Gate::authorize('update_comment', $comment); 
        $request->validate([
            'title'=>'required|min:4',
            'desc'=>'required|max:256'
        ]);
        $comment->title = request('title');
        $comment->desc = request('desc'); 
        if ($comment->save()) {
            return redirect()->route('article.show', $comment->article_id)->with('status', 'Comment update success');
        } else {
            return redirect()->back()->with('status', 'Update failed');
        } 
    }

    public function delete($id){
        $comment = Comment::findOrFail($id);
        Gate::authorize('update_comment', $comment);
        if ($comment->delete()) return redirect()->route('article.show', $comment->article_id)->with('status', 'Delete success');
        else return redirect()->route('article.show', $comment->article_id)->with('status', 'Delete comment failed');
    }

    public function accept(Comment $comment){
        $comment->accept = true;
        $comment->save();
        return redirect()->route('comment.index');
    }
    public function reject(Comment $comment){
        $comment->accept = false;
        $comment->save();
        return redirect()->route('comment.index');
    }
}