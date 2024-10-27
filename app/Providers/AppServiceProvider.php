<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\Order\OrderInterface;
use App\Service\Order\OrderService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderInterface::class, function ($app) {
            // 現在沒有訂單type，故不需判斷訂單類別
            return new OrderService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
