<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Movie;

class MovieComponent extends Component
{
    public $movies; // Array to store all movies

    public function mount()
    {
        $this->movies = Movie::all(); // Get all movies once during mount
    }



    public function render()
    {
         // Get all movies from the database
        return view('livewire.movie-component')->layout('layouts.app');
    }
}
