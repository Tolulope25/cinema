@extends('layouts.app')
@section('content')
@if(session()->has('error')) {{session()->get('error')}} @endif
<h1>Start Payment</h1>

<h2>Order Details</h2>
<p>Order ID: {{ $order->id }}</p>
<p>Customer: {{ $order->first_name }} {{ $order->last_name }}</p>
<p>Total Amount: {{ '₦' . number_format($order->total_amount, 2) }}</p>

{{-- <h3>Items:</h3>
<ul>
    @foreach($order->orderItems as $item)
        <li>{{ $item->product->name }} - {{ '₦' . number_format($item->price, 2) }} x {{ $item->quantity }}</li>
    @endforeach
</ul> --}}

<form action="{{route('pay')}}" method="POST">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <input type="text" name="email" placeholder="Email Address" value="{{ $order->email }}" readonly> <br><br>
    <input type="number" name="amount" placeholder="Enter amount" value="{{ $order->total_amount}}" readonly > <br><br>
    <button type="submit">Submit</button>
</form>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




@endsection
