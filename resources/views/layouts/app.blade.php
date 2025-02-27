<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema World</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        /* Move the 'Home' link a bit to the left */
        .navbar-nav .nav-item:first-child {
            margin-left: -5px;
        }

        /* Add hover effect */
        .navbar-nav .nav-link:hover {
            color: #f0ad4e !important; /* Orange hover color */
            transition: color 0.3s ease;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Dark Mode Navbar -->
    <header>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand fs-3 fw-bold text-warning">Cinema <span class="text-white">World</span></a>

            <!-- Navbar Toggler for Small Screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>



            <!-- Navbar Links -->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link text-white fw-semibold">Home</a>
                    </li>
                    <li class="nav-item">
                        {{-- <a href="{{ route('about') }}" class="nav-link text-white fw-semibold">About</a> --}}
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('now.showing') }}" class="nav-link text-white fw-semibold">Now Showing</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('upcoming') }}" class="nav-link text-white fw-semibold">Up Coming</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white fw-semibold">Admin</a>
                    </li>

                    <!-- Show logout link if the user is authenticated -->
                    @guest
                        @if (Route::has('login') || Route::has('register'))
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle text-white fw-semibold" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Login / Register
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if (Route::has('login'))
                                        <li><a href="{{ route('login') }}" class="dropdown-item">Login</a></li>
                                    @endif
                                    @if (Route::has('register'))
                                        <li><a href="{{ route('register') }}" class="dropdown-item">Register</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @else
                        <!-- Show logout link if the user is authenticated -->
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link text-white fw-semibold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </li>

                        <!-- Form for logging out -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </nav>


    </header>

    <!-- Main Content Section -->
    <main class="container py-5">
        @yield('content') <!-- Content from individual views will be injected here -->
    </main>

    <!-- Footer Section (Optional) -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 Cinema World. All Rights Reserved.</p>
            <p>Designed by <a href="https://www.templatemonster.com/" class="text-white">TemplateMonster</a></p>
        </div>
    </footer>

    <!-- Bootstrap JS and Popper.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
