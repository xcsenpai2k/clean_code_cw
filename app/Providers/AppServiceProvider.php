<?php

namespace App\Providers;

use App\Repositories\BaseRepositoryInterface;
use App\Repositories\CountryRepositoryInterface;
use App\Repositories\CustomerAddressRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\CountryRepository;
use App\Repositories\Eloquent\CustomerAddressRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\OrderDetailRepository;
use App\Repositories\Eloquent\OrderItemRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\OrderDetailRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            BaseRepositoryInterface::class, 
            BaseRepository::class
        );
        $this->app->bind(
            CountryRepositoryInterface::class, 
            CountryRepository::class
        );
        $this->app->bind(
            CustomerAddressRepositoryInterface::class, 
            CustomerAddressRepository::class
        );
        $this->app->bind(
            CustomerRepositoryInterface::class, 
            CustomerRepository::class
        );
        $this->app->bind(
            PaymentRepositoryInterface::class, 
            PaymentRepository::class
        );
        $this->app->bind(
            ProductRepositoryInterface::class, 
            ProductRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class, 
            UserRepository::class
        );
        $this->app->bind(
            OrderRepositoryInterface::class, 
            OrderRepository::class
        );
        $this->app->bind(
            OrderDetailRepositoryInterface::class, 
            OrderDetailRepository::class
        );
        $this->app->bind(
            OrderItemRepositoryInterface::class, 
            OrderItemRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
