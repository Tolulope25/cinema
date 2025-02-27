@extends('layouts.app')
@section('content')
<h1>Payment Failed</h1>
@if(session()->has('error'))
    <p>{{ session('error') }}</p>
@else
    <p>Something went wrong. Please try again.</p>
@endif
<a href="{{ url('/') }}">Return to Home</a>

@endsection
