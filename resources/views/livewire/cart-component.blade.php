<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 1rem;">
    @if (session()->has('error'))
        <div style="padding: 1rem; margin-bottom: 2rem; background-color: #fff4f4; border-radius: 8px; border: 1px solid #fecaca; color: #991b1b; position: relative;">
            <strong style="display: block; margin-right: 20px;">{{ session()->get('error') }}</strong>
            <button type="button" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #991b1b; cursor: pointer;" data-bs-dismiss="alert" aria-label="close">&times;</button>
        </div>
    @endif

    @if (session()->has('success'))
        <div style="padding: 1rem; margin-bottom: 2rem; background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0; color: #166534; position: relative;">
            <strong style="display: block; margin-right: 20px;">{{ session()->get('success') }}</strong>
            <button type="button" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #166534; cursor: pointer;" data-bs-dismiss="alert" aria-label="close">&times;</button>
        </div>
    @endif

    <h1 style="font-size: 2rem; font-weight: 600; color: #1f2937; margin-bottom: 2rem; text-align: center;">Shopping Cart</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        @forelse($cartItems as $item)
            <div style="background-color: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); overflow: hidden; position: relative; transition: transform 0.2s;" wire:key="cart-item-{{ $item->schedule_id }}" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                @if(!$item->schedule || !$item->schedule->movie)
                    <div style="position: absolute; top: 0; right: 0; background-color: #ef4444; color: white; padding: 0.5rem 1rem; border-bottom-left-radius: 8px; font-size: 0.875rem;">
                        Invalid Item
                    </div>
                @endif

                @if($item->schedule)
                    @if ($item->schedule->movie->poster_url)
                        <div style="position: relative;">
                            <img
                                src="{{ asset('movie/poster/' . $item->schedule->movie->poster_url) }}"
                                alt="{{ $item->schedule->movie->title }}"
                                loading="lazy"
                                style="width: 100%; height: 200px; object-fit: cover;"
                            >
                        </div>
                    @endif

                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                            {{ $item->schedule->movie->title }}
                        </h3>

                        <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <p style="color: #4b5563; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Schedule:</span><br>
                                {{ \Carbon\Carbon::parse($item->schedule->show_date)->format('l, F j, Y') }}<br>
                                {{ \Carbon\Carbon::parse($item->schedule->show_time)->format('g:i A') }}
                            </p>
                            <p style="color: #4b5563;">
                                <span style="font-weight: 600;">Theatre:</span><br>
                                {{ $item->schedule->theatre->name ?? 'Not Available' }}
                            </p>
                        </div>

                        <div style="background-color: #f0f9ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <span style="display: block; color: #0369a1; font-weight: 600; margin-bottom: 0.5rem;">
                                Ticket Price: ₦{{ number_format($item->final_price, 2) }}
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding: 0.5rem; background-color: #f8fafc; border-radius: 8px;">
                            <span style="font-weight: 600; color: #475569;">Quantity:</span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <button
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #e5e7eb; border: none; border-radius: 50%; color: #4b5563; cursor: pointer; transition: background-color 0.2s;"
                                    wire:click="updateQuantity({{ $item->schedule_id }}, 'decrease')"
                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                    onmouseover="this.style.backgroundColor='#d1d5db'"
                                    onmouseout="this.style.backgroundColor='#e5e7eb'"
                                >
                                    -
                                </button>
                                <span style="font-size: 1.125rem; font-weight: 600; color: #1f2937; min-width: 2rem; text-align: center;">{{ $item->quantity }}</span>
                                {{-- @if(session()->has('error'))
                                <div class="tooltip">
                                    {{ session()->get('error') }}
                                </div>
                            @endif --}}
                            <button
                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #e5e7eb; border: none; border-radius: 50%; color: #4b5563; cursor: pointer; transition: background-color 0.2s;"
                            wire:click="{{ $item->quantity < 10 ? 'updateQuantity('.$item->schedule_id.', \'increase\')' : '' }}"
                            {{ $item->quantity >= 10 ? 'disabled' : '' }}
                            onmouseover="if(this.disabled){this.style.backgroundColor='#e5e7eb'} else {this.style.backgroundColor='#d1d5db'}"
                            onmouseout="this.style.backgroundColor='#e5e7eb'"
                        >
                            +
                        </button>

                            </div>
                        </div>

                        @if($item->quantity >= 10)
                            <p style="color: #ef4444; font-size: 0.875rem; margin-bottom: 1rem; text-align: right;">
                                Maximum ticket limit reached
                            </p>
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <span style="font-size: 1.25rem; font-weight: 600; color: #1f2937;">
                                Total: ₦{{ number_format($item->final_price * $item->quantity, 2) }}
                            </span>
                            <button
                                wire:click="removeFromCart({{ $item->schedule_id }})"
                                style="background-color: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#dc2626'"
                                onmouseout="this.style.backgroundColor='#ef4444'"
                            >
                                Remove
                            </button>
                        </div>
                        @if($cartItems->isNotEmpty())
                        <div style="margin-top: 2rem; text-align: right;">
                            <button
                                wire:click="proceedToOrder"
                                style="background-color: #10b981; color: white; padding: 1rem 2rem; border-radius: 8px; border: none; font-size: 1.125rem; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#059669'; this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.backgroundColor='#10b981'; this.style.transform='translateY(0)'"
                            >
                                Proceed to Order
                            </button>
                        </div>
                    @endif
                    </div>
                @else
                    <p style="color: #ef4444; padding: 1rem; text-align: center;">No available schedule for this item.</p>
                @endif
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                <p style="font-size: 1.5rem; color: #4b5563; margin-bottom: 1rem;">Your cart is empty</p>
                <p style="color: #6b7280; margin-bottom: 2rem;">Explore our movies and add some tickets!</p>
                <a href="{{ route('home') }}" style="display: inline-block; background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                    Browse Movies
                </a>
            </div>
        @endforelse


    </div>


</div>
