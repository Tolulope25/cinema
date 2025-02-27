<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Theatre;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'theatre_id',
        'seat_number',
        'is_available',

    ];


    public function theatre()
    {
        return $this->belongsTo(Theatre::class);
    }

    public function orders()
{
    return $this->belongsToMany(Order::class, 'order_seat');
}

}
