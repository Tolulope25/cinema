
@extends('layouts.app')
@section('title', 'Create Theatre')
@section('content')
@if (session()->has('error'))
<div class="alert alert-danger alert-dismissable fade show" role="alert">
    <strong>{{ session()->get('error') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
</div>
@endif
@if(session()->has('success'))

<div class="alert alert-success alert-dismissable fade show" role="alert">
    <strong>{{ session()->get('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
</div>
@endif

<div class="container">



<form action="{{ route('send.theatre') }}" method="POST">
    @csrf
    <label for="name">Theater Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="rows_count">Number of Rows:</label>
    <input type="number" id="rows_count" name="rows_count" required>

    <label for="seats_per_row">Seats per Row:</label>
    <input type="number" id="seats_per_row" name="seats_per_row" required>

    <label for="screen_type">Screen Type:</label>
    <select id="screen_type" name="screen_type">
        <option value="2D">2D</option>
        <option value="3D">3D</option>
        <option value="IMAX">IMAX</option>
        <option value="4DX">4DX</option>
    </select>

    <label for="is_active">Is Active:</label>
    <input type="checkbox" id="is_active" name="is_active" value="1" checked>

    <label for="capacity"> Capacity:</label>
    <textarea id="capacity" name="capacity"></textarea>

    <button type="submit">Create Theater</button>
</form>
</div>
@endsection
