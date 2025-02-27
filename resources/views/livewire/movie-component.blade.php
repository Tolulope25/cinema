<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 1rem;">
    <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 2rem; text-align: center; color: #333;">All Movies</h1>

    @if($movies->isEmpty())
        <div style="text-align: center; color: #666; padding: 3rem 0;">
            <p style="font-size: 1.25rem;">No movies found.</p>
        </div>
    @else
        <div>
            <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 2rem; text-align: center; color: #333;">
                Fresh <span style="color: #3490dc;">Movies</span>
            </h3>

            <!-- Movie Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 2rem; padding: 0 1rem;">
                @foreach ($movies as $movie)
                    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s ease; border: 1px solid #ddd; padding-bottom: 1rem; height: auto; display: flex; flex-direction: column;">
                        <div style="position: relative; padding-top: 150%; background: #f0f0f0;">
                            @if ($movie->poster_url)
                                <img
                                    src="{{ asset('movie/poster/' . $movie->poster_url) }}"
                                    alt="{{ $movie->title }}"
                                    loading="lazy"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </div>

                        <div style="padding: 1rem; text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $movie->title }}
                            </h4>
                            <p style="color: #555; font-size: 1rem; font-weight: 400; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                {{ $movie->description }}
                            </p>

                            <a href="{{ route('movies.show', $movie->id) }}" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #3490dc; color: white; text-decoration: none; border-radius: 0.375rem; transition: background-color 0.2s ease; font-size: 1rem; font-weight: 600; text-align: center; width: 100%; max-width: 180px; margin: 0 auto;">
                                Read More
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
