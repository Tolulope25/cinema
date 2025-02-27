<div class="container mx-auto px-6 py-10 max-w-7xl">

    <!-- Session Alerts -->
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissable fade show mb-6" role="alert">
            <strong>{{ session()->get('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
    @endif
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissable fade show mb-6" role="alert">
            <strong>{{ session()->get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
    @endif

    <!-- Page Title -->
    <h1 class="text-4xl font-semibold mb-8 text-gray-900 text-center">Complete Your Order</h1>

    <!-- Cart Items Section -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-10">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Order Summary</h2>

        @if(count($cartItems) > 0)
            @foreach($cartItems as $item)
                <div class="flex items-center border-b py-6">
                    <img src="{{ asset('storage/movies/' . $item['movie_image']) }}"
                         alt="{{ $item['movie_title'] }}"
                         class="w-24 h-36 object-cover rounded-md shadow-md">

                    <div class="ml-6 flex-grow">
                        <h3 class="text-xl font-semibold text-gray-800">{{ $item['movie_title'] }}</h3>
                        <p class="text-gray-600 mt-1">Quantity: {{ $item['quantity'] }}</p>
                        <p class="text-gray-600 mt-1">Price: ₦{{ number_format($item['price'], 2) }}</p>
                    </div>
                </div>
            @endforeach

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal:</span>
                    <span>₦{{ number_format($totalAmount - $bookingFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600 mt-2">
                    <span>Booking Fee:</span>
                    <span>₦{{ number_format($bookingFee, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg text-gray-800 mt-6">
                    <span>Total:</span>
                    <span>₦{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Your cart is empty.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Browse Movies
                </a>
            </div>
        @endif
    </div>

    <!-- Order Form Section -->
    @if(count($cartItems) > 0)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold mb-8 text-gray-800">Customer Information</h2>

            <form wire:submit.prevent="submitOrder">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- First Name -->
                    <div class="form-group">
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="first_name"
                               wire:model="first_name"
                               value="{{ auth()->check() ? auth()->user()->first_name : '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="Enter your first name"
                               required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="last_name"
                               wire:model="last_name"
                               value="{{ auth()->check() ? auth()->user()->last_name : '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="Enter your last name"
                               required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address
                        </label>
                        <input type="email"
                               id="email"
                               wire:model="email"
                               value="{{ auth()->check() ? auth()->user()->email : '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="your@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel"
                               id="phone"
                               wire:model="phone"
                               value="{{ auth()->check() ? auth()->user()->phone : '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="Enter your phone number"
                               required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address_line" class="block text-sm font-medium text-gray-700 mb-1">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address_line"
                                  wire:model="address_line"
                                  rows="4"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                  placeholder="Enter your full address"
                                  required>{{ auth()->check() ? auth()->user()->address : '' }}</textarea>
                        @error('address_line')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-200">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
