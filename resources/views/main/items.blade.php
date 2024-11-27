@extends('layout')
@section('content')
    <table class="table">
        <thead>
            <tr>
                <th class="col">Date</th>
                <th class="col">Name</th>
                <th class="col">Description</th>
                <th class="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{$item['date']}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['shortDesc']}}</td>
                    <td>
                        <img src="{{ $item['preview_image'] }}" alt="Изображеие">
                    </td>
                </tr>         
            @endforeach
        </tbody>
    </table>
@endsection