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
    .hero-section {
        position: relative;
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)),
            url('https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        overflow: hidden;
    }

    .hero-text {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 2rem;
    }

    .hero-text h1 {
        font-size: 4.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        animation: fadeInDown 1s ease-out;
    }

    .hero-text p {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        font-weight: 300;
        line-height: 1.6;
        animation: fadeInUp 1s ease-out 0.5s;
        animation-fill-mode: both;
    }

    .cta-btn {
        display: inline-block;
        background-color: #e50914;
        color: white;
        padding: 1rem 2.5rem;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 30px;
        text-decoration: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: fadeInUp 1s ease-out 1s;
        animation-fill-mode: both;
    }

    .cta-btn:hover {
        background-color: #f40612;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
        color: white;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .hero-text h1 {
            font-size: 2.5rem;
        }

        .hero-text p {
            font-size: 1.1rem;
        }
    }
</style>

<div class="hero-section">
    <div class="hero-text">
        <h1>Where Magic Meets The Screen</h1>
        <p>Immerse yourself in a world of endless stories, unforgettable characters, and cinematic masterpieces. Your next adventure awaits in every frame.</p>
        <a href="#explore" class="cta-btn">Discover Movies</a>
    </div>
</div>


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
    {{-- <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">Copyright &copy; 2010 <a href="#" class="text-light">SiteName</a> - All Rights Reserved</p>
        <p class="mb-0">Design by <a href="http://www.templatemonster.com/" class="text-light">TemplateMonster</a></p>
    </footer> --}}
</div>

@endsection
