<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\TheatreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Livewire\MovieComponent;
use App\Http\Livewire\CartComponent;
use App\Http\Livewire\OrderComponent;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('about', [HomeController::class, 'about'])->name('about');
Route::get('now-showing', [AdminController::class, 'nowShowing'])->name('now.showing');
Route::get('upcoming', [AdminController::class, 'getUpcomingMovies'])->name('upcoming');
Route::get('movie-show/{id}', [HomeController::class, 'showMovie'])->name('movie.show');



    Route::get('admin/create-genre', [AdminController::class, 'createGenre'])->name('create.genre');
    Route::get('admin/create-movies', [AdminController::class, 'createMovies'])->name('create.movies');
    Route::get('admin/create-languages', [AdminController::class, 'createLanguages'])->name('create.language');
    Route::get('admin/edit-movies/{id}', [AdminController::class, 'editMovies'])->name('edit.movies');
    Route::get('delete-movies/{id}', [AdminController::class, 'delete'])->name('delete.movies');
    Route::post('store-movies', [AdminController::class, 'storeMovie'])->name('send.movie');
    Route::post('store-genre', [AdminController::class, 'storeGenre'])->name('send.genre');
    Route::post('store-language', [AdminController::class, 'storeLanguage'])->name('send.language');

    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('admin');
    Route::put('users/update-role', [DashboardController::class, 'updateUserRole'])->name('update.user');




Route::post('store-theatre', [TheatreController::class, 'storeTheatre'])->name('send.theatre');
Route::get('admin/create-theatre', [TheatreController::class, 'createTheatre'])->name('create.theatre');
Route::put('update-movies/{id}', [AdminController::class, 'updateMovies'])->name('update.movies');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login-User', [AuthController::class, 'loginUser'])->name('login.user');
Route::post('register-User', [AuthController::class, 'registerUser'])->name('register.user');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('show/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/cart', function () {
    return view('cart'); // Ensure 'cart' is the Blade view containing the Livewire component
})->name('cart.index');
Route::post('/cart/add/{id}', [CartComponent::class, 'addToCart'])->name('cart.add');

Route::get('/order', function () {
    return view('order'); // This view will include the Livewire component
})->name('order.index');

Route::get('order-confirmation/{orderId}', [OrderController::class, 'confirmation'])->name('order.confirmation');

Route::get('pay/form/{orderId}', [PaymentController::class, 'pay'])->name('pay.form');
Route::post('pay', [PaymentController::class, 'make_payment'])->name('pay');
Route::get('/pay/callback', [PaymentController::class, 'payment_callback'])->name('pay.callback');






// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
