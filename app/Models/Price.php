<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = ['movie_id',  'base_price', 'discount_percentage', 'final_price'
];

public function prices()
{
    return $this->hasMany(Price::class);
}

public function movie()
{
    return $this->belongsTo(Movie::class);
}

public function schedule()
{
    return $this->belongsTo(Schedule::class);
}
}
