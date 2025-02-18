<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;


class OrderController extends Controller
{
public function confirmation($orderId){

   // Retrieve the order with related orderItems, schedule, movie, and theatre
   $order = Order::with('orderItems.schedule.movie', 'orderItems.schedule.theatre')->findOrFail($orderId);




   // You can also calculate total amount if needed here
   $totalAmount = $order->total_amount;
   $bookingFee = 200; // Example static booking fee

   // Pass the data to the view
   return view('order-confirmation', compact('order', 'totalAmount', 'bookingFee'));



}
}
