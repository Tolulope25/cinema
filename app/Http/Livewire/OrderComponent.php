<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Price;

class OrderComponent extends Component
{
    public $cartItems = [];
    public $totalAmount = 0;
    public $bookingFee = 200;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address_line;

    public function mount()
    {
        $this->loadCartItems();
        

    if (auth()->check()) {
        $user = auth()->user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address_line = $user->address;
    }
    }

    public function loadCartItems()
    {

        $user = Auth::user();

        if ($user) {
            // For authenticated users
            $cart = Cart::where('user_id', $user->id)
                ->with(['cartItems.schedule.movie'])
                ->first();

            $this->cartItems = $cart ? $cart->cartItems->map(function ($cartItem) {
                // Ensure schedule and movie exist
                if (!$cartItem->schedule || !$cartItem->schedule->movie) {
                    return [
                        'schedule_id' => $cartItem->schedule_id,
                        'movie_id' => null,
                        'movie_title' => 'Unknown Movie',
                        'movie_image' => 'default.jpg',
                        'quantity' => $cartItem->quantity,
                        'price' => 0,
                    ];
                }

                // Get price from the Price model
                $price = Price::where('movie_id', $cartItem->schedule->movie->id)->first()?->final_price ?? 0;

                return [
                    'schedule_id' => $cartItem->schedule_id,
                    'movie_id' => $cartItem->schedule->movie->id,
                    'movie_title' => $cartItem->schedule->movie->title, // Make sure your Movie model uses 'title'
                    'movie_image' => $cartItem->schedule->movie->image ?? 'default.jpg',
                    'quantity' => $cartItem->quantity,
                    'price' => $price,
                ];
            })->toArray() : [];
        } else {
            // For guest users
            $guestCart = session()->get('guest_cart', []);

            $this->cartItems = collect($guestCart)->map(function ($item) {
                $schedule = Schedule::with('movie')->find($item['schedule_id']);

                if (!$schedule || !$schedule->movie) {
                    return [
                        'schedule_id' => $item['schedule_id'],
                        'movie_id' => null,
                        'movie_title' => 'Unknown Movie',
                        'movie_image' => 'default.jpg',
                        'quantity' => $item['quantity'],
                        'price' => 0,
                    ];
                }

                $price = $item['price'] ?? Price::where('movie_id', $schedule->movie->id)->first()?->final_price ?? 0;

                return [
                    'schedule_id' => $item['schedule_id'],
                    'movie_id' => $schedule->movie->id,
                    'movie_title' => $schedule->movie->title, // Make sure your Movie model uses 'title'
                    'movie_image' => $schedule->movie->image ?? 'default.jpg',
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ];
            })->toArray();
        }

        // Calculate subtotal and total
        $subtotal = collect($this->cartItems)->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        $this->totalAmount = $subtotal + $this->bookingFee;
    }

    public function submitOrder()
    {
        $validatedData = $this->validate([
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z]+$/',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'required|string|max:11',
            'address_line' => 'required|string',
        ]);

            // Add additional fields to validated data
    $validatedData['user_id'] = Auth::check() ? Auth::id() : null; // Allow guest users
    $validatedData['status'] = 'pending';
    $validatedData['total_amount'] = $this->totalAmount; // Ensure $this->totalAmount is defined


        $order = Order::create($validatedData);

        foreach ($this->cartItems as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'movie_id' => $item['movie_id'],
                'schedule_id' => $item['schedule_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }



        return redirect()->route('order.confirmation', ['orderId' => $order->id])
            ->with('success', 'Order placed successfully!');
    }



    public function render()
    {
        return view('livewire.order-component', [
            'bookingFee' => $this->bookingFee,
        ])->layout('layouts.app');
    }
}
