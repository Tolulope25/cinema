<?php

namespace App\Providers;
use Livewire\Livewire;
use App\Http\Livewire\CartComponent;
use App\Http\Livewire\MovieComponent;
use App\Http\Livewire\OrderComponent;



use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('cart-component', CartComponent::class);
        Livewire::component('movie-component', MovieComponent::class);
        Livewire::component('order-component', OrderComponent::class);
     }

}
