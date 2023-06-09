<?php
namespace Ant\Payment;

use Illuminate\Support\Facades\View;
use Ant\Payment\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->register(RouteServiceProvider::class);
        
        //
        $this->loadMigrationsFrom([
            __DIR__.'/resources/database/migrations',   
        ]);
        
        $this->loadViewsFrom(__DIR__.'/resources/views', 'payment');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}