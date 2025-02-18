<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class HomeController extends Controller
{
    public function home(){
        $movie = Movie::all();
        return view ('home', compact('movie'));
    }


public function about(){
    return view ('about');
}





public function showMovie($id)
{
    $movie = Movie::findOrFail($id);
    return view('movie-show', ['movie' => $movie]);
}

// public function nowShowing(){
//     $movies = $this->getNowShowingMovies();
//     return view('now-showing', compact('movies'));

// }



}

