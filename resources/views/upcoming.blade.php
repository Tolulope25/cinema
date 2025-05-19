@extends('layouts.app')

@section('content')
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

@if ($upcomingMovies->isEmpty())
<div style="text-align: center; padding: 2rem; background-color: #f3f4f6; border-radius: 8px; margin-bottom: 2rem;">
    <p style="font-size: 1.25rem; color: #555;">There are no upcoming movies at the moment.</p>
</div>
@else
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
    @foreach ($upcomingMovies as $movie)
        <div style="background: white; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s ease; border: 1px solid #eee;">
            <div style="position: relative; padding-top: 150%;">
                @if ($movie->poster_url)
                    <img
                        src="{{ asset('movie/poster/' . $movie->poster_url) }}"
                        alt="{{ $movie->title }}"
                        loading="lazy"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                    >
                @endif
            </div>

            <div style="padding: 1rem;">
                <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $movie->title }}
                </h4>

                <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                    {{ $movie->description }}
                </p>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('movies.show', $movie->id) }}"
                        style="flex: 1; text-align: center; padding: 0.5rem; background-color: #3490dc; color: white; text-decoration: none; border-radius: 0.375rem; transition: background-color 0.2s ease; font-size: 0.875rem;"
                        onmouseover="this.style.backgroundColor='#2779bd'"
                        onmouseout="this.style.backgroundColor='#3490dc'">
                        Read More
                    </a>
                </div>

                <div style="margin-top: 1rem;">
                    @foreach ($movie->schedules as $schedule)
                        <form action="{{ route('cart.add', $schedule->id) }}" method="POST" style="margin-bottom: 1rem;">
                            @csrf
                            <button type="submit"
                                style="width: 100%; text-align: center; padding: 0.5rem; background-color: #38a169; color: white; text-decoration: none; border-radius: 0.375rem; transition: background-color 0.2s ease; font-size: 0.875rem;"
                                onmouseover="this.style.backgroundColor='#2f855a'"
                                onmouseout="this.style.backgroundColor='#38a169'">
                                Book Schedule at {{ $schedule->show_time->format('h:i A') }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
