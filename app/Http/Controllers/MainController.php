<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $jsonPath = public_path('articles.json');

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        return view('main/items', ['items' => $jsonData]);
    }
}
