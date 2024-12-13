@extends('layout')
@section('content')
@use('\App\Models\User', 'User')

@if(session('status') == 'Delete success')
  <div class="alert alert-success">
      {{ session('status') }}
  </div>
@elseif(session('status') == 'Delete comment failed')
  <div class="alert alert-danger">
    {{ session('status') }}
  </div>
@endif

@if(session('status') == 'Comment update success')
  <div class="alert alert-success">
      {{ session('status') }}
  </div>
@elseif(session('status') == 'Update failed')
  <div class="alert alert-danger">
    {{ session('status') }}
  </div>
@endif

<div class="card text-center">
    <div class="card-header">
      Author: {{ $auth->name }}
    </div>
    <div class="card-body">
      <h5 class="card-title">{{ $article->name }}</h5>
      <p class="card-text">{{ $article->text }}</p>
      <div class="d-flex align-items-center gap-2 justify-content-center">
          <a href="/article/{{ $article->id }}/edit" class="btn btn-primary">Edit article</a>
          <form action="/article/{{ $article->id }}" method="POST">
            @method('DELETE')
            @csrf
            <button type="submit" class="btn btn-primary">Delete article</button>
          </form>
      </div>

    </div>
  </div>

  <h3 class="text-center">Add comment</h3>
  @if ($errors->any())
  <div class="alert-danger">
     <ul>
      @foreach($errors->all() as $error)
        <li>{{$error}}</li>
      @endforeach
    </ul>
  </div>
@endif
<form action="/comment" method="POST">
  @csrf
    <div class="mb-3">
    <label for="title" class="form-label">Name</label>
    <input type="text" class="form-control" id="title" name="title">
  </div>
  <div class="mb-3">
    <label for="desc" class="form-label">Description</label>
    <input type="text" class="form-control" id="desc" name="desc">
  </div>
  <input type="hidden" name="article_id" value="{{ $article->id }}">
  <button type="submit" class="btn btn-primary">Save comment</button>
</form>
<h3 class="text-center">Comments</h3>
<div class="row">
@foreach($comments as $comment)
  <div class="col-sm-6 mb-3 mb-sm-0 mt-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{$comment->title}}</h5>
        <p class="card-text">{{$comment->desc}}</p>
        @can('update_comment', $comment)
        <a href="/comment/{{$comment->id}}/edit" class="btn btn-primary">Comment update</a>
        <a href="/comment/{{$comment->id}}/delete" class="btn btn-warning">Comment delete</a>      
        @endcan
      </div>
    </div>
  </div>
@endforeach
</div>
@endsection