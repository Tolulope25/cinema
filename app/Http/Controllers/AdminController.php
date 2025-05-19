<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Theatre;
use App\Models\Schedule;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class AdminController extends Controller
{
    public function createMovies(){
        $genres = Genre::all();
        $languages = Language::all();
        $theatres = Theatre::all();
        return view('admin.create-movies', compact('genres', 'languages','theatres'));
    }

    public function storeMovie(Request $request)
    {
        DB::beginTransaction(); // Start transaction

        try {
            // Log the incoming request data for debugging
            Log::debug('Request Data: ', $request->all());

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    Rule::unique('movies')->where(function ($query) use ($request) {
                        return $query->where('title', $request->title)
                                     ->whereDate('release_date', $request->release_date);
                    })
                ],
                'description' => 'required|string',
                'duration' => 'required|numeric',
                'release_date' => 'date|nullable',
                'end_date' => 'nullable|date|after_or_equal:release_date',
                'language_ids' => 'required|array',
                'theatre_ids' => 'required|array|exists:theatres,id',
                'show_times.*' => 'nullable|date_format:H:i',
                'show_date.*' => 'nullable|date',
                'director' => 'string|nullable',
                'genre_ids' => 'required|array',
                'cast' => 'string|nullable',
                'poster_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'trailer_url' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240',
                'base_price' => 'required|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'final_price' => 'nullable|numeric|min:0',
            ], [
                'title.unique' => 'This movie already exists with the same title and release date.',
                'show_time.unique' => 'This time slot is already booked for the selected date in this theater.',
            ]);

            if ($validator->fails()) {
                Log::debug('Validation Errors:', $validator->errors()->toArray());
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            // Handle file uploads
            $poster_url = $this->uploadFile($request->file('poster_url'), 'poster');
            $trailer_url = $this->uploadFile($request->file('trailer_url'), 'trailers');

            // Create the movie
            $movie = Movie::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'duration' => $request->input('duration'),
                'release_date' => $request->input('release_date'),
                'end_date' => $request->input('end_date'),
                'director' => $request->input('director'),
                'cast' => $request->input('cast'),
                'poster_url' => $poster_url,
                'trailer_url' => $trailer_url,
            ]);

            // Price calculations
            $basePrice = $request->input('base_price');
            $discountPercentage = $request->input('discount_percentage') ?? 0;
            $finalPrice = $basePrice - ($basePrice * ($discountPercentage / 100));

            if ($finalPrice < 0) {
                throw new \Exception('Final price cannot be less than zero.');
            }

            Price::create([
                'movie_id' => $movie->id,
                'base_price' => $basePrice,
                'final_price' => $finalPrice,
                'discount_percentage' => $discountPercentage,
            ]);

            // Schedule logic
            foreach ($request->input('theatre_ids') as $index => $theatre_id) {
                $show_time = $request->input('show_times')[$index] ?? null;
                $show_date = $request->input('show_date')[$index] ?? null;

                // Skip if both are null
                if (!$show_time && !$show_date) {
                    continue;
                }

                $showStartTime = $show_time ? Carbon::parse($show_time) : null;
                $showEndTime = $showStartTime ? $showStartTime->copy()->addMinutes($movie->duration) : null;

                if ($showStartTime && $showEndTime) {
                    $existingSchedule = Schedule::where('theatre_id', $theatre_id)
                        ->where('show_date', $show_date)
                        ->where(function ($query) use ($showStartTime, $showEndTime) {
                            $query->whereExists(function ($subquery) use ($showStartTime, $showEndTime) {
                                $subquery->select(DB::raw(1))
                                    ->from('movies')
                                    ->whereColumn('movies.id', 'schedules.movie_id') // Join with movies table
                                    ->where(function ($innerQuery) use ($showStartTime, $showEndTime) {
                                        $innerQuery
                                        ->where('schedules.show_time', '<', $showEndTime)
                                        ->where(DB::raw("DATE_ADD(schedules.show_time, INTERVAL movies.duration MINUTE)"), '>', $showStartTime);
                                    });
                            });
                        })
                        ->exists();

                    if ($existingSchedule) {
                        throw new \Exception('Schedule conflict found for theatre ID: ' . $theatre_id);
                    }

                    $bufferConflict = Schedule::where('theatre_id', $theatre_id)
    ->where('show_date', $show_date)
    ->where(function ($query) use ($showStartTime, $showEndTime) {
        $query->whereExists(function ($subquery) use ($showStartTime, $showEndTime) {
            $subquery->select(DB::raw(1))
                ->from('movies')
                ->whereColumn('movies.id', 'schedules.movie_id')
                ->where(function ($innerQuery) use ($showStartTime, $showEndTime) {
                    // Ensure 10-minute gap before and after any movie
                    $innerQuery
                        ->where(DB::raw("DATE_ADD(schedules.show_time, INTERVAL movies.duration + 10 MINUTE)"), '>', $showStartTime)
                        ->where('schedules.show_time', '<', DB::raw("DATE_ADD('$showEndTime', INTERVAL 10 MINUTE)"));
                });
        });
    })
    ->exists();

if ($bufferConflict) {
    throw new \Exception('Schedule conflict: There must be a 10-minute gap before and after any movie in theatre ID ' . $theatre_id);
}

                    Schedule::create([
                        'theatre_id' => $theatre_id,
                        'show_date' => $show_date,
                        'show_time' => $show_time,
                        'movie_id' => $movie->id,
                    ]);
                }
            }

            // Attach genres and languages
            $movie->genres()->attach($request->input('genre_ids'));
            $movie->languages()->attach($request->input('language_ids'));

            // Commit transaction
            DB::commit();

            return redirect('/admin/create-movies')->with('success', 'Movie created successfully');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error storing movie: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


protected function uploadFile($file, $directory)
{
    if ($file) {
        $fileName = uniqid() . '-' . $directory . '.' . $file->extension();
        $file->move(public_path('movie/' . $directory), $fileName);
        return $fileName;
    }
    return null;
}



    public function editMovies($id){
        $movie = Movie::findOrFail($id);
        $genres = Genre::all();
        $languages = Language::all();
        $theatres = Theatre::all();

        if (!$movie) {
            return redirect()->back()->with('error', 'Movie not found.');
        }



        return view('admin/edit-movies', compact('movie', 'genres', 'languages', 'theatres'));
    }

    public function updateMovies(Request $request, $id)
    {
        DB::beginTransaction(); // Start transaction

        try {
            // Log the incoming request data for debugging
            Log::debug('Request Data: ', $request->all());

            // Find the movie or fail
            $movie = Movie::findOrFail($id);

            // Validate input
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    Rule::unique('movies')->where(function ($query) use ($request, $id) {
                        return $query->where('title', $request->title)
                                     ->whereDate('release_date', $request->release_date)
                                     ->where('id', '!=', $id);
                    }),
                ],
                'description' => 'required|string',
                'duration' => 'required|numeric',
                'release_date' => 'date|nullable',
                'end_date' => 'nullable|date|after_or_equal:release_date',
                'language_ids' => 'required|array',
                'theatre_ids' => 'required|array|exists:theatres,id',
                'show_times.*' => 'nullable|date_format:H:i',
                'show_date.*' => 'nullable|date',
                'director' => 'string|nullable',
                'genre_ids' => 'required|array',
                'cast' => 'string|nullable',
                'poster_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'trailer_url' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240',
                'base_price' => 'required|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                Log::debug('Validation Errors:', $validator->errors()->toArray());
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            // Handle file updates
            if ($request->hasFile('poster_url')) {
                if ($movie->poster_url && file_exists(public_path('movie/poster/' . $movie->poster_url))) {
                    unlink(public_path('movie/poster/' . $movie->poster_url));
                }
                $poster_url = $this->uploadFile($request->file('poster_url'), 'poster');
                $movie->poster_url = $poster_url;
            }

            if ($request->hasFile('trailer_url')) {
                if ($movie->trailer_url && file_exists(public_path('movie/trailers/' . $movie->trailer_url))) {
                    unlink(public_path('movie/trailers/' . $movie->trailer_url));
                }
                $trailer_url = $this->uploadFile($request->file('trailer_url'), 'trailers');
                $movie->trailer_url = $trailer_url;
            }

            // Update movie details
            $movie->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'duration' => $request->input('duration'),
                'release_date' => $request->input('release_date'),
                'end_date' => $request->input('end_date'),
                'director' => $request->input('director'),
                'cast' => $request->input('cast'),
            ]);

            // Price calculations
            $basePrice = $request->input('base_price');
            $discountPercentage = $request->input('discount_percentage') ?? 0;
            $finalPrice = $basePrice - ($basePrice * ($discountPercentage / 100));

            if ($finalPrice < 0) {
                throw new \Exception('Final price cannot be less than zero.');
            }

            $movie->price()->updateOrCreate(
                ['movie_id' => $movie->id],
                [
                    'base_price' => $basePrice,
                    'final_price' => $finalPrice,
                    'discount_percentage' => $discountPercentage,
                ]
            );

            // Remove existing schedules
            Schedule::where('movie_id', $movie->id)->delete();

            // Create new schedules
            foreach ($request->input('theatre_ids') as $index => $theatre_id) {
                $show_time = $request->input('show_times')[$index] ?? null;
                $show_date = $request->input('show_date')[$index] ?? null;

                if (!$show_time && !$show_date) {
                    continue;
                }

                $showStartTime = $show_time ? Carbon::parse($show_time) : null;
                $showEndTime = $showStartTime ? $showStartTime->copy()->addMinutes($movie->duration) : null;

                if ($showStartTime && $showEndTime) {
                    $existingSchedule = Schedule::where('theatre_id', $theatre_id)
                    ->where('show_date', $show_date)
                    ->where(function ($query) use ($showStartTime, $showEndTime) {
                        $query->whereExists(function ($subquery) use ($showStartTime, $showEndTime) {
                            $subquery->select(DB::raw(1))
                                ->from('schedules')
                                ->join('movies', 'movies.id', '=', 'schedules.movie_id') // Join with movies table
                                ->where('schedules.theatre_id', '=', 'schedules.theatre_id')
                                ->where(function ($innerQuery) use ($showStartTime, $showEndTime) {
                                    $innerQuery->where('schedules.show_time', '<', $showEndTime)
                                               ->where(DB::raw("DATE_ADD(schedules.show_time, INTERVAL movies.duration MINUTE)"), '>', $showStartTime);
                                });
                        });
                    })
                    ->exists();

                  // Add the buffer conflict check here
    $bufferConflict = Schedule::where('theatre_id', $theatre_id)
    ->where('show_date', $show_date)
    ->where(function ($query) use ($showStartTime, $showEndTime) {
        $query->whereExists(function ($subquery) use ($showStartTime, $showEndTime) {
            $subquery->select(DB::raw(1))
                ->from('movies')
                ->whereColumn('movies.id', 'schedules.movie_id')
                ->where(function ($innerQuery) use ($showStartTime, $showEndTime) {
                    // Ensure 10-minute gap before and after any movie
                    $innerQuery
                        ->where(DB::raw("DATE_ADD(schedules.show_time, INTERVAL movies.duration + 10 MINUTE)"), '>', $showStartTime)
                        ->where('schedules.show_time', '<', DB::raw("DATE_ADD('$showEndTime', INTERVAL 10 MINUTE)"));
                });
        });
    })
    ->exists();

if ($bufferConflict) {
    throw new \Exception('Schedule conflict: There must be a 10-minute gap before and after any movie in theatre ID ' . $theatre_id);
}

Schedule::create([
    'theatre_id' => $theatre_id,
    'show_date' => $show_date,
    'show_time' => $show_time,
    'movie_id' => $movie->id,
]);
                }
            }

            // Sync genres and languages
            $movie->genres()->sync($request->input('genre_ids'));
            $movie->languages()->sync($request->input('language_ids'));

            DB::commit();

            return redirect('/')->with('success', 'Movie updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating movie: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }




    public function createGenre(){
        return view('admin.create-genre');
    }
    public function storeGenre(Request $request){
        $validator = validator::make($request->all(),[
            'name' =>'required|string|unique:genres',
        ]);

        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        try {
            $formFields = [
                'name' => $request->input('name'),
            ];
            $genres = Genre::create($formFields);

            if ($genres) {
                return redirect('/admin/create-genre')->with('success', 'Genre created successfully');
            } else {
                return redirect()->back()->with('error', 'Something went wrong');
            }
        } catch (\Exception $e) {
            Log::error('Error creating genre: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the genre.');
        }
    }

    public function createLanguages(){
        return view('admin.create-languages');
    }
    public function storeLanguage(Request $request){
        $validator = Validator::make($request->all(),[
            'name' =>'required|string|unique:languages',
        ]);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $formFields = [
            'name' => $request->input('name'),
        ];
        $language = Language::create($formFields);
        if($language){
            return redirect('/admin/create-languages')->with('success', 'Language created successfully');
        } else{
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function nowShowing()
    {
        $now = Carbon::now('Africa/Lagos');

        // Enable query logging to capture executed queries
        \DB::enableQueryLog();

        // Log the current date and time for debugging purposes
        \Log::debug('Current Date and Time:', ['now' => $now]);

        // Run the query to get now showing movies
        $nowShowing = Movie::whereHas('schedules', function ($query) use ($now) {
         $query->where('show_date', $now->toDateString())
                ->where('show_time', '<=', $now->toTimeString())
                ->whereRaw(
                    "DATE_ADD(show_time, INTERVAL (SELECT duration FROM movies WHERE movies.id = schedules.movie_id) MINUTE) >= ?",
                    [$now->toTimeString()]
                );
        })
        ->where(function ($query) use ($now) {
           $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now->toDateString());
        })
        ->with('schedules')
        ->get();

        // Log the query log for debugging
        \Log::debug('Executed Query', ['query_log' => \DB::getQueryLog()]);

        // Log the result of the query for debugging
        \Log::debug('Now Showing Movies:', ['nowShowing' => $nowShowing]);

        return view('now-showing', compact('nowShowing'));
    }



    public function getUpcomingMovies()
    {
        $now = Carbon::now('Africa/Lagos');
        $movies = Movie::with('schedules')->get();

        $upcomingMovies = Movie::whereHas('schedules', function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                // Include movies showing in the future
                $q->where('show_date', '>', $now->toDateString())
                  ->orWhere(function ($q) use ($now) {
                      // Include today’s movies but only future times
                      $q->where('show_date', '=', $now->toDateString())
                        ->whereTime('show_time', '>', $now->toTimeString());
                  });
            });
        })
        ->with(['schedules' => function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                $q->where('show_date', '>', $now->toDateString())
                  ->orWhere(function ($q) use ($now) {
                      $q->where('show_date', '=', $now->toDateString())
                        ->whereTime('show_time', '>', $now->toTimeString());
                  });
            })->orderBy('show_date', 'asc')
              ->orderBy('show_time', 'asc');
        }])
        ->get();

        // Pass upcomingMovies to the view
        return view('upcoming', compact('upcomingMovies'));
    }


    public function delete($id){
        $movie = Movie::find($id);
         // Perform the deletion
    $movie->delete();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Movie deleted successfully.');
    }



}

