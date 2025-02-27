<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    public function show($id){
        $movie = Movie::findOrFail($id);
        return view('movie-show', compact('movie'));

    }
}
