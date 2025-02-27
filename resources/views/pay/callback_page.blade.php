@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payment {{ $order->status === 'paid' ? 'Successful' : 'Failed' }}</h1>

    <div class="details">
        <p><strong>Reference:</strong> {{ $order->reference }}</p>
        <p><strong>Order ID:</strong> {{ $order->id }}</p>  <!-- Corrected this line to use $order->id -->
        <p><strong>Amount Paid:</strong> ₦{{ number_format($order->total_amount, 2) }}</p>  <!-- Corrected to use total_amount -->
    </div>

    <h3>Order Details:</h3>
    <div class="order-details">
        @foreach($order->orderItems as $orderItem)
            <div class="order-item">
                <h4>{{ $orderItem->schedule->movie->title }}</h4>
                <span class="font-semibold">Schedule:</span>
                {{ \Carbon\Carbon::parse($orderItem->schedule->show_date)->format('l, F j, Y') }} at
                {{ \Carbon\Carbon::parse($orderItem->schedule->show_time)->format('g:i A') }}
                <br>
                <p><strong>Cinema:</strong> {{ $orderItem->schedule->theatre->name }}</p>
                <p><strong>Seats:</strong> {{ $orderItem->quantity }}</p>
                <p><strong>Price:</strong> ₦{{ number_format($orderItem->price, 2) }}</p>
            </div>
            <hr>
        @endforeach
    </div>

    <p>Thank you for your payment. You will receive a confirmation email shortly.</p>
    <a href="{{ route('home') }}">Go back to Home</a>
</div>

@endsection
