<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'release_date',
        'end_date',

        'director',
        'cast',        // Cast members stored as a text field
        'poster_url',  // URL to the movie poster
        'trailer_url'

    ];



     // Accessor to get the cast as an array
     public function getCastArrayAttribute()
     {
         return explode(',', $this->attributes['cast']);
     }

     // Optionally, if you want to save the cast as a string directly
     public function setCastAttribute($value)
     {
         $this->attributes['cast'] = is_array($value) ? implode(',', $value) : $value;
     }
     public function genres(){
         return $this->belongsToMany(Genre::class);
     }
     public function languages()
     {
         return $this->belongsToMany(Language::class);
     }

     public function schedules()
     {
         return $this->hasMany(Schedule::class);
     }

     public function theatres()
    {
        return $this->belongsToMany(Theatre::class, 'movie_theatre')->withTimestamps();
    }

    public function price()
{
    return $this->hasOne(Price::class);
}

}
