@extends('layouts.app')
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

<div class="mb-3">
    <h4>Create Genre</h4>
    <form action="{{ route('send.genre') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Genre Name</label>
            <input type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   name="name"
                   id="name"
                   placeholder="Enter genre name"
                   value="{{ old('name') }}"> <!-- Use old() to retain input -->

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <button class="btn btn-primary mt-3" type="submit">Create Genre</button>
    </form>
</div>

@endsection
