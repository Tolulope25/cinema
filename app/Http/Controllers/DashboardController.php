<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Language;
use App\Models\Theatre;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

        public function index()
        {

            $users = User::all();
            $movies = Movie::all();
            $totalMovies = Movie::count();
            $todayBookings = Order::count('status', 'paid');

            $scheduleStats = Schedule::with(['theatre', 'movie'])
                ->get()
                ->map(function ($schedule) {
                    $seatsBooked = OrderItem::whereHas('order', function ($query) {
                            $query->where('status', 'paid');
                        })
                        ->where('schedule_id', $schedule->id)
                        ->sum('quantity');

                    return [
                        'theatre_id' => $schedule->theatre->id,
                        'schedule_id' => $schedule->id,
                        'movie_title' => $schedule->movie->title,
                        'show_date' => $schedule->show_date,
                        'show_time' => $schedule->show_time,
                        'theatre_capacity' => $schedule->theatre->capacity,
                        'theatre_name'     => $schedule->theatre->name,
                        'seats_booked' => $seatsBooked,
                        'seats_remaining' => $schedule->theatre->capacity - $seatsBooked
                    ];
                });


                $orders = Order::with('orderItems.schedule.movie')
                ->where('status','paid')
                ->latest()
                ->paginate(5);

            $totalTicketsSold = $orders->sum('order_items_count');
            // dd($scheduleStats);


            return view('admin.dashboard', [
                'orders' => $orders,
                'totalTicketsSold' => $totalTicketsSold,
                'totalMovies' => $totalMovies,
                'scheduleStats' => $scheduleStats,
                'users' => $users,
                'todayBookings' => $todayBookings,
                'movies' => $movies,
                // 'genres' => Genre::all(),
                // 'languages' => Language::all(),
                // 'theatres' => Theatre::all(),

            ]);
        }



        public function updateUserRole(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'role' => 'required|string|in:user,admin',
    ]);

    $user = User::findOrFail($request->user_id);

    $user->update(['role' => $request->role]);

    return redirect()->back()->with('success', 'User role updated successfully!');
}

public function todayBookings(){
    $todayBookings = Order::where('statu', 'paid');


}

}
