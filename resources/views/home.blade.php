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
<style>
    /* Hero Section Styling */
    .hero-section {
        position: relative;
        background-image: url('https://images.unsplash.com/photo-1560264414-35b9e15f8f2f'); /* Replace with a cinema-related image */
        background-size: cover;
        background-position: center;
        color: white;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 0 1rem;
    }

    .hero-text {
        background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent black background for text readability */
        padding: 2rem;
        border-radius: 10px;
    }

    .hero-text h1 {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 1rem;
        text-transform: uppercase;
    }

    .hero-text p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        font-style: italic;
    }

    .cta-btn {
        background-color: #3490dc;
        color: white;
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
        font-weight: bold;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .cta-btn:hover {
        background-color: #2779bd;
    }

    @media (max-width: 768px) {
        .hero-text h1 {
            font-size: 2rem;
        }

        .hero-text p {
            font-size: 1rem;
        }
    }
</style>


    <!-- Welcome Box -->
    <div class="hero-section">
        <div class="hero-text">
            <h1>Popcorn and Movies</h1>
            <p>Your perfect movie experience starts here with a bowl of popcorn!</p>
            <a href="#explore" class="cta-btn">Explore Now</a>
        </div>
    </div>

    <div class="container mt-5" id="explore">
        <div class="row">
            <div class="col-12 text-center">
                <h2>Enjoy Your Favorite Movies</h2>
                <p>Popcorn and a good movie is all you need to make your night unforgettable.</p>
            </div>
        </div>


    <!-- Fresh Movies Section -->
    <div class="content">
        <h3 class="text-center">Fresh <span class="text-primary">Movies</span></h3>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="images/1page-img2.jpg" class="card-img-top img-fluid" alt="Toy Story 3">
                    <div class="card-body text-center">
                        <h4>Toy Story 3</h4>
                        <p>Egetnunc nunc mattitor curabiturpiscipis nec ac hac pellus sem intesque sociis.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="images/1page-img3.jpg" class="card-img-top img-fluid" alt="Prince of Persia">
                    <div class="card-body text-center">
                        <h4>Prince of Persia: Sands of Time</h4>
                        <p>Dolorem malesuada anterdum quis vitae.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="images/1page-img4.jpg" class="card-img-top img-fluid" alt="Twilight Saga">
                    <div class="card-body text-center">
                        <h4>The Twilight Saga: Eclipse</h4>
                        <p>Quisque felit odio ut nunc convallis semper sente ris feugiat.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Livewire Movie Component -->
    <div class="my-4">
        @livewire('movie-component')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">Copyright &copy; 2010 <a href="#" class="text-light">SiteName</a> - All Rights Reserved</p>
        <p class="mb-0">Design by <a href="http://www.templatemonster.com/" class="text-light">TemplateMonster</a></p>
    </footer>
</div>

@endsection
