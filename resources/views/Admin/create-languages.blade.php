@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Language</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('send.language') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Language Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Add Language</button>
    </form>
</div>
@endsection
