<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'address_line',
    'total_amount',
    'status',
   ' reference'


];

public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
public function schedule()
{
    return $this->belongsTo(Schedule::class);
}

}
