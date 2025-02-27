@extends('layouts.app')

@section('content')
    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div style="padding: 1rem; margin: 1rem auto; max-width: 1200px; background-color: #fff4f4; border-left: 4px solid #dc2626; color: #991b1b;">
            <strong>{{ session()->get('error') }}</strong>
            <button type="button" style="float: right; background: none; border: none; color: #991b1b;" data-bs-dismiss="alert" aria-label="close">&times;</button>
        </div>
    @endif

    @if (session()->has('success'))
        <div style="padding: 1rem; margin: 1rem auto; max-width: 1200px; background-color: #f0fdf4; border-left: 4px solid #16a34a; color: #166534;">
            <strong>{{ session()->get('success') }}</strong>
            <button type="button" style="float: right; background: none; border: none; color: #166534;" data-bs-dismiss="alert" aria-label="close">&times;</button>
        </div>
    @endif

    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <div style="background-color: #ffffff; border: 1px solid #f0f0f0; border-radius: 16px; overflow: hidden;">
            {{-- Header Section with Title and Rating --}}
            <div style="padding: 2rem; background: linear-gradient(to right, #1e293b, #334155); color: white;">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $movie->title }}</h1>
                <div style="display: flex; gap: 1rem; font-size: 0.9rem; color: #cbd5e1;">
                    <span>{{ $movie->duration }} minutes</span>
                    <span>•</span>
                    <span>{{ $movie->rating }}</span>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap;">
                {{-- Movie Poster Section --}}
                <div style="flex: 1 1 300px;">
                    @if ($movie->poster_url)
                        <div style="position: relative; margin: -4rem 2rem 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                            <img
                                src="{{ asset('movie/poster/' . $movie->poster_url) }}"
                                alt="{{ $movie->title }}"
                                loading="lazy"
                                style="width: 100%; height: auto; border-radius: 12px;">
                        </div>
                    @endif
                </div>

                {{-- Movie Details Section --}}
                <div style="flex: 1 1 500px; padding: 2rem;">
                    {{-- Quick Info Pills --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 2rem;">
                        @foreach ($movie->genres->pluck('name') as $genre)
                            <span style="padding: 0.5rem 1rem; background-color: #f8fafc; border-radius: 20px; font-size: 0.875rem; color: #475569;">{{ $genre }}</span>
                        @endforeach
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">Synopsis</h2>
                        <p style="color: #475569; line-height: 1.6;">{{ $movie->description }}</p>
                    </div>

                    {{-- Movie Details Grid --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">Cast & Crew</h3>
                            <div style="color: #475569;">
                                <p style="margin-bottom: 0.5rem;"><strong style="color: #334155;">Director:</strong> {{ $movie->director }}</p>
                                <p style="margin-bottom: 0.5rem;"><strong style="color: #334155;">Cast:</strong> {{ $movie->cast }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">Movie Info</h3>
                            <div style="color: #475569;">
                                <p style="margin-bottom: 0.5rem;"><strong style="color: #334155;">Release Date:</strong> {{ $movie->release_date }}</p>
                                <p style="margin-bottom: 0.5rem;"><strong style="color: #334155;">Language:</strong> {{ $movie->languages->pluck('name')->join(', ') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Trailer Section --}}
                    @if ($movie->trailer_url)
                        <div style="margin-top: 2rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">Movie Trailer</h3>
                            <div style="position: relative; padding-bottom: 56.25%; border-radius: 12px; overflow: hidden;">
                                <iframe
                                    src="{{ $movie->trailer_url }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 12px;">
                                </iframe>
                            </div>
                        </div>
                    @endif

                    {{-- Add to Cart Button --}}
                    <div style="margin-top: 2rem;">
                        <form action="{{ route('cart.add', $movie->id) }}" method="POST">
                            @csrf
                            {{-- <button
                                type="submit"
                                style="background-color: #3b82f6; color: white; padding: 1rem 2rem; border-radius: 30px; border: none; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.backgroundColor='#3b82f6'; this.style.transform='translateY(0)'">
                                Book Tickets
                            </button> --}}
                        </form>
                    </div>


                </div>
            </div>
        </div>

        {{-- Showtimes Section --}}
        @if ($movie->schedules->isNotEmpty())
            <div style="margin-top: 2rem; background-color: white; border: 1px solid #f0f0f0; border-radius: 16px; padding: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #0f172a; margin-bottom: 2rem;">Available Showtimes</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach ($movie->schedules as $schedule)
                        <div style="padding: 1.5rem; background-color: #f8fafc; border-radius: 12px; transition: transform 0.2s ease;"
                             onmouseover="this.style.transform='translateY(-4px)'"
                             onmouseout="this.style.transform='translateY(0)'">
                            <h4 style="font-size: 1.125rem; font-weight: 600; color: #0f172a; margin-bottom: 1rem;">{{ $schedule->theatre->name }}</h4>
                            <p style="color: #475569; margin-bottom: 0.5rem;">{{ \Carbon\Carbon::parse($schedule->show_date)->format('D, M d, Y') }}</p>
                            <p style="color: #475569; margin-bottom: 1rem; font-size: 1.25rem; font-weight: 600;">{{ \Carbon\Carbon::parse($schedule->show_time)->format('h:i A') }}</p>
                            <form action="{{ route('cart.add', $schedule->id) }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    style="width: 100%; background-color: #22c55e; color: white; padding: 0.75rem; border-radius: 8px; border: none; font-weight: 500; cursor: pointer; transition: background-color 0.2s ease;"
                                    onmouseover="this.style.backgroundColor='#16a34a'"
                                    onmouseout="this.style.backgroundColor='#22c55e'">
                                    Select Seats
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    
@endsection
