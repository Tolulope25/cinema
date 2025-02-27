@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <h1 style="font-size: 2.5rem; font-weight: 700; color: #0f172a; margin-bottom: 2rem; align-item-center">Now Showing</h1>

    @if ($nowShowing->isEmpty())
        <div style="text-align: center; padding: 4rem; background-color: #f8fafc; border-radius: 16px; color: #64748b;">
            <p>No movies are currently showing at the moment.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            @foreach ($nowShowing as $movie)
                <div style="background-color: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden;">
                    <img  src="{{ asset('movie/poster/' . $movie->poster_url) }}" alt="{{ $movie->title }}" style="width: 100%; height: 300px; object-fit: cover;">
                    <div style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">{{ $movie->title }}</h2>
                        <p style="color: #475569; line-height: 1.6; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $movie->description }}</p>
                        <a href="{{ route('movies.show', $movie->id) }}" style="display: inline-block; width: 100%; background-color: #22c55e; color: white; padding: 0.75rem; border-radius: 8px; border: none; font-weight: 500; cursor: pointer; text-align: center; text-decoration: none; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#16a34a'" onmouseout="this.style.backgroundColor='#22c55e'">
                            View Showtimes
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
