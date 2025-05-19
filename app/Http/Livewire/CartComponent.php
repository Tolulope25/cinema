<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Price;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class CartComponent extends Component
{
    public $cartItems = [];

    protected $listeners = [
        'cartUpdated' => 'loadCartItems',
        'clearCartAfterPayment' => 'clearCart'  // Add this line
    ];
    public $totalAmount = 0;


    public function mount()
    {
        $this->loadCartItems();
    }


    public function loadCartItems()
    {
        $this->cartItems = collect();
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
                ->with(['cartItems.schedule.movie']) // Eager load relationships
                ->first();

            if ($cart) {
                $this->cartItems = $cart->cartItems->map(function ($cartItem) {
                    // Find price based on movie from the schedule
                    $movie = $cartItem->schedule->movie;
                    $price = Price::where('movie_id', $movie->id)->first();

                    $finalPrice = $price ? ($price->final_price ?? $price->base_price ?? 0) : 0;

                    return (object) [
                        'schedule_id' => $cartItem->schedule_id,
                        'movie_id' => $movie->id,
                        'quantity' => $cartItem->quantity,
                        'schedule' => $cartItem->schedule,
                        'movie' => $movie,
                        'base_price' => $price->base_price ?? 0,
                        'final_price' => $finalPrice,
                    ];
                });
            }
        } else {
            // For guest users, load the cart from the session
            $guestCart = session()->get('guest_cart', []);
            $this->cartItems = collect(array_map(function ($item) {
                // Get schedule details for the guest user
                $schedule = Schedule::find($item['schedule_id']);

                if (!$schedule) {
                    return null; // Skip if schedule doesn't exist
                }

                // Get the associated price for the movie dynamically
                $price = Price::where('movie_id', $schedule->movie_id)->first(); // Ensure price is correctly fetched

                // Default price if not found
                $finalPrice = $price ? ($price->final_price ?? $price->base_price ?? 0) : 0;

                // Dynamically calculate the price and return the item
                return (object) [
                    'schedule_id' => $item['schedule_id'],
                    'movie_id' => $item['movie_id'],
                    'quantity' => $item['quantity'],
                    'schedule' => $schedule,
                    'base_price' => $price->base_price ?? 0, // Use the fetched price base_price
                    'final_price' => $finalPrice,  // Dynamically calculated final price from the `prices` table
                ];
            }, $guestCart));
        }

        // Calculate the total amount dynamically
        $this->totalAmount = $this->cartItems->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });
    }

    public function addToCart($scheduleId)
    {
        try {
            $schedule = Schedule::with(['movie', 'theatre'])->findOrFail($scheduleId);
            $newQuantity = 1;

            return DB::transaction(function() use ($schedule, $newQuantity) {
                $theatreCapacity = $schedule->theatre->capacity;

                // Get confirmed seats from OrderItems
                $confirmedSeats = OrderItem::whereHas('order', function($query) {
                    $query->where('status', 'paid');
                })
                ->where('schedule_id', $schedule->id)
                ->lockForUpdate()
                ->sum('quantity');

                // Get seats in cart
                $cartSeats = CartItem::where('schedule_id', $schedule->id)
                    ->lockForUpdate()
                    ->sum('quantity');

                $totalBooked = $confirmedSeats + $cartSeats;

                if ($totalBooked + $newQuantity > $theatreCapacity) {
                    $remainingSeats = $theatreCapacity - $totalBooked;
                    return redirect()->back()
                        ->with('error', "No seat available.");
                }

                if (Auth::check()) {
                    $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

                    $existingCartItem = $cart->cartItems()->where('schedule_id', $schedule->id)->first();
                    if ($existingCartItem && $existingCartItem->quantity >= 10) {
                        return redirect()->back()->with('error', 'Maximum ticket limit of 10 reached.');
                    }

                    // Remove existing cart items and add the new schedule
                    if ($cart->cartItems()->exists()) {
                        $cart->cartItems()->delete();
                    }

                    $cart->cartItems()->create([
                        'movie_id' => $schedule->movie_id,
                        'schedule_id' => $schedule->id,
                        'quantity' => $newQuantity,
                    ]);
                } else {
                    $guestCart = session()->get('guest_cart', []);
                    $itemKey = array_search($schedule->id, array_column($guestCart, 'schedule_id'));

                    if ($itemKey !== false) {
                        if ($guestCart[$itemKey]['quantity'] >= 10) {
                            return redirect()->back()->with('error', 'Maximum ticket limit of 10 reached.');
                        }
                        $guestCart[$itemKey]['quantity'] += $newQuantity;
                    } else {
                        $guestCart = []; // Clear the guest cart for the new schedule
                        $guestCart[] = [
                            'movie_id' => $schedule->movie_id,
                            'schedule_id' => $schedule->id,
                            'quantity' => $newQuantity,
                        ];
                    }

                    session()->put('guest_cart', $guestCart);
                }

                return redirect()->route('cart.index')
                    ->with('success', 'Added to cart successfully.');
            });
        } catch (\Exception $e) {
            \Log::error('Cart addition failed:', [
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add to cart. Please try again.');
        }
    }




    public function updateQuantity($scheduleId, $action)
    {
        $schedule = Schedule::with('theatre')->find($scheduleId);

        if (!$schedule) {
            session()->flash('error', 'Schedule not found.');
            return;
        }

        $theatreCapacity = $schedule->theatre->capacity;

        // Start the DB transaction to avoid conflicts
        DB::beginTransaction();

        try {
            // Get confirmed seats from paid orders - add this!
            $confirmedSeats = OrderItem::whereHas('order', function($query) {
                $query->where('status', 'paid');
            })
            ->where('schedule_id', $scheduleId)
            ->lockForUpdate()
            ->sum('quantity');

            // Get all cart seats except current user's
            $otherCartSeats = CartItem::where('schedule_id', $scheduleId)
                ->when(Auth::check(), function($query) {
                    $query->where('cart_id', '!=', Auth::user()->cart->id);
                })
                ->lockForUpdate()
                ->sum('quantity');

            $totalBookedSeats = $confirmedSeats + $otherCartSeats;

            if (Auth::check()) {
                // Authenticated user logic
                $cart = Auth::user()->cart;
                $cartItem = $cart->cartItems()->where('schedule_id', $scheduleId)->first();

                if ($cartItem) {
                    if ($action === 'increase') {
                        if ($cartItem->quantity >= 10) {
                            session()->flash('error', 'Maximum ticket limit of 10 reached.');
                            DB::rollBack();
                            return;
                        }
                        // Check if adding 1 more seat would exceed the capacity
                        if ($totalBookedSeats + $cartItem->quantity + 1 > $theatreCapacity) {
                            session()->flash('error', 'Not enough seats available.');
                            DB::rollBack();
                            return;
                        }
                        $cartItem->increment('quantity');
                        $cartItem->save();
                    } elseif ($action === 'decrease' && $cartItem->quantity > 1) {
                        $cartItem->decrement('quantity');
                        $cartItem->save();
                    }
                }
            } else {
                // Guest user logic
                $guestCart = session()->get('guest_cart', []);
                $guestCart = is_array($guestCart) ? $guestCart : [];

                $itemIndex = collect($guestCart)->search(function ($item) use ($scheduleId) {
                    return $item['schedule_id'] == $scheduleId;
                });

                if ($itemIndex !== false) {
                    if ($action === 'increase') {
                        $currentQuantity = $guestCart[$itemIndex]['quantity'];


                        // Prevent guest from having more than 10 tickets
                        if ($currentQuantity >= 10) {
                            session()->flash('error', 'Maximum ticket limit of 10 reached.');
                            DB::rollBack();
                            return;
                        }
                        // Check if adding 1 more seat would exceed the capacity
                        if ($totalBookedSeats + $currentQuantity + 1 > $theatreCapacity) {
                            session()->flash('error', 'Not enough seats available.');
                            DB::rollBack();
                            return;
                        }
                        $guestCart[$itemIndex]['quantity']++;
                    } elseif ($action === 'decrease' && $guestCart[$itemIndex]['quantity'] > 1) {
                        $guestCart[$itemIndex]['quantity']--;
                    }
                } elseif ($action === 'increase') {
                    // Check if adding a new item will exceed the capacity
                    if ($totalBookedSeats + 1 > $theatreCapacity) {
                        session()->flash('error', 'Not enough seats available.');
                        DB::rollBack();
                        return;
                    }
                    $price = Price::where('movie_id', $schedule->movie_id)->first();
                    $finalPrice = $price ? $price->final_price : 0;

                    $guestCart[] = [
                        'movie_id' => $schedule->movie_id,
                        'schedule_id' => $scheduleId,
                        'quantity' => 1,
                        'final_price' => $finalPrice,
                    ];
                }

                session()->put('guest_cart', $guestCart);
            }

            DB::commit();
            $this->loadCartItems();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'There was an issue updating the cart.');
            \Log::error('Cart update failed', ['error' => $e->getMessage()]);
        }
    }
    public function clearCart()
{
    try {
        DB::beginTransaction();

        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                $cart->cartItems()->delete();
            }
        } else {
            session()->forget('guest_cart');
        }

        DB::commit();
        $this->loadCartItems(); // Refresh cart items

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to clear cart:', ['error' => $e->getMessage()]);
    }
}






    public function removeFromCart($scheduleId)
    {
        if (Auth::check()) {
            // Authenticated user remove logic
            $cart = Auth::user()->cart;
            $cartItem = $cart->cartItems()->where('schedule_id', $scheduleId)->first();

            if ($cartItem) {
                $cartItem->delete();
            }
        } else {
            // Guest user remove logic
            $guestCart = session()->get('guest_cart', []);

            $guestCart = collect($guestCart)
                ->reject(function ($item) use ($scheduleId) {
                    return $item['schedule_id'] == $scheduleId;
                })
                ->values()
                ->all();

            session()->put('guest_cart', $guestCart);
        }

        $this->loadCartItems();
    }

    public function proceedToOrder()
    {
        if (Auth::check()) {
            // Ensure cart is saved for authenticated users
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        } else {
            // Ensure guest cart is saved in session
            $guestCart = session()->get('guest_cart', []);
            session()->put('guest_cart', $guestCart);


        }

        return redirect()->route('order.index');
    }


     public function render()
    {
        return view('livewire.cart-component',[ 'cartItems' => $this->cartItems,
        'totalAmount' => $this->totalAmount,])->layout('layouts.app');
    }
}

