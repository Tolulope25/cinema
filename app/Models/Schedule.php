<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'movie_id',
        'theatre_id',
        'show_date',
        'show_time'
    ];

    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function theatre()
    {
        return $this->belongsTo(Theatre::class);
    }
    public function price()
    {
        return $this->belongsTo(Price::class, 'movie_id');
    }
    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

public function orders()
{
    return $this->hasMany(Order::class);
}
}
