@extends('layout')
@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-danger">
        {{ session('status') }}
    </div>
@endif

<form action="/article/{{ $article->id }}" method="POST">
    @method('PUT')
    @csrf
    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control" name="date" id="date" value="{{ $article->date }}">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" id="name" value="{{ $article->name }}">
    </div>
    <div class="mb-3">
        <label for="desc" class="form-label">Description</label>
        <input type="text" class="form-control" name="desc" id="desc" value="{{ $article->text }}">
    </div>

    <button type="submit" class="btn btn-primary">Update article</button>
</form>

@endsection