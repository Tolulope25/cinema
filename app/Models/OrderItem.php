<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'movie_id',
        'schedule_id',
        'quantity',
        'price',
        'total'

    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function order()
{
    return $this->belongsTo(Order::class, 'order_id');
}
}
