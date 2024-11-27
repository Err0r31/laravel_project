@extends('layout')
@section('content')
    <div class="container">
        <p>{{$data['city']}}</p>
        <p>{{$data['street']}}</p>
        <p>{{$data['home']}}</p>
    </div>
@endsection