<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
           'cart_id', 'movie_id', 'schedule_id', 'quantity'
    ];


    public function cart()
    {
        return $this->belongsTo(Cart::class);
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
