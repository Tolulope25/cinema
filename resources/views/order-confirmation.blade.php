@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h1 class="text-center text-success mb-4">Order Confirmation</h1>

        @if ($order)
            <div class="alert alert-success text-center">
                Thank you for your order, <strong>{{ $order->first_name }} {{ $order->last_name }}</strong>!
            </div>
            <div class="mb-4 text-center">
                <h5>Order ID: <span class="badge bg-primary">{{ $order->id }}</span></h5>
                <p class="text-muted">You will receive a confirmation email at <strong>{{ $order->email }}</strong>.</p>
            </div>

            <h3 class="mb-3">Order Summary:</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Movie</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->movie->title ?? 'Unknown Movie' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₦{{ number_format($item->price, 2) }}</td>
                            <td>₦{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <h4>Booking Fee:</h4>
                <p class="text-muted">A booking fee has been applied to your order. The total includes this fee.</p>

                   <p>Total Amount: <span class="text-success">&#8358;{{ number_format($order->total_amount, 2) }}</span></p>


            </div>
        @else
            <div class="alert alert-warning text-center">
                No order data available. Please check your order details and try again.
            </div>
        @endif
        <div>
            <a href="{{ route('pay.form', ['orderId' => $order->id]) }}" class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Payment</a>
        </div>
    </div>
</div>
<div id="footer" style="width: 100%">
    <div class="left">
        <div class="right">
            <div class="footerlink">
                <p class="lf">Copyright &copy; 2010 <a href="#">SiteName</a> - All Rights Reserved</p>
                <p class="rf">Design by <a href="http://www.templatemonster.com/">TemplateMonster</a></p>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
@endsection
