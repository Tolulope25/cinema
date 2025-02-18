@extends('layouts.app')
@section('title', 'Create Movie')
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

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Create Movie</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('send.movie') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" name="title" id="title" placeholder="Movie Title">
                        @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" id="duration" name="duration" placeholder="Enter duration">
                        @error('duration')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="release_date" class="form-label">Release Date</label>
                        <input type="date" class="form-control @error('release_date') is-invalid @enderror" value="{{ old('release_date') }}" id="release_date" name="release_date">
                        @error('release_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" id="end_date" name="end_date">
                        @error('end_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>



                    <div class="col-md-6">
                        <label for="director" class="form-label">Director</label>
                        <input type="text" class="form-control @error('director') is-invalid @enderror" value="{{ old('director') }}" id="director" name="director" placeholder="Director's name">
                        @error('director')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cast" class="form-label">Cast</label>
                    <input type="text" class="form-control @error('cast') is-invalid @enderror" value="{{ old('cast') }}" id="cast" name="cast" placeholder="Comma-separated cast">
                    @error('cast')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="poster_url" class="form-label">Poster</label>
                        <input type="file" class="form-control @error('poster_url') is-invalid @enderror" id="poster_url" name="poster_url" aria-label="Upload Poster">
                        @error('poster_url')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="trailer_url" class="form-label">Trailer</label>
                        <input type="file" class="form-control @error('trailer_url') is-invalid @enderror" id="trailer_url" name="trailer_url" aria-label="Upload Trailer">
                        @error('trailer_url')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="genre_ids" class="form-label">Genres</label>
                    <div id="genre_ids" class="form-check">
                        @foreach($genres as $genre)
                            <div class="form-check">
                                <input class="form-check-input @error('genre_ids') is-invalid @enderror" type="checkbox" id="genre_{{ $genre->id }}" name="genre_ids[]" value="{{ $genre->id }}" {{ (collect(old('genre_ids'))->contains($genre->id)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="genre_{{ $genre->id }}">
                                    {{ $genre->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('genre_ids')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="language_ids" class="form-label">Languages</label>
                    <div id="language_ids" class="form-check">
                        @foreach($languages as $language)
                            <div class="form-check">
                                <input class="form-check-input @error('language_ids') is-invalid @enderror" type="checkbox" id="language_{{ $language->id }}" name="language_ids[]" value="{{ $language->id }}" {{ (collect(old('language_ids'))->contains($language->id)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="language_{{ $language->id }}">
                                    {{ $language->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('language_ids')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div id="theatre-dates">
                    @foreach($theatres as $index => $theatre)
                    <div class="theatre-schedule">
                        <input type="hidden" name="theatre_ids[]" value="{{ $theatre->id }}">
                        <div>
                            <label for="show_date_{{ $index }}">Show Date for {{ $theatre->name }}</label>
                            <input type="date" name="show_date[]" id="show_date_{{ $index }}" r>
                        </div>

                        <div>
                            <label for="show_time_{{ $index }}">Show Time for {{ $theatre->name }}</label>
                            <input type="time" name="show_times[]" >
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Price Details -->
                <h3>Set Prices</h3>
              <div>
                <label for="base_price">Price</label>
                <input type="number" name="base_price" >
              </div>
                <!-- Discount -->
                <div>
                    <label for="discount_percentage">Discount Percentage (Optional)</label>
                    <input type="number" name="discount_percentage" step="0.01" min="0" max="100">
                </div>

                <button class="btn btn-success mt-4" type="submit">Create Movie</button>
            </form>
        </div>
    </div>
</div>

@endsection
