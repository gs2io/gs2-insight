<?php

namespace App\Providers;

use App\Domain\Gs2Domain;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set('Asia/Tokyo');
        View::share('timezone', 'Asia/Tokyo');
        try {
            View::share('permission', (new Gs2Domain())->permission());
        } catch (\Exception) {
            View::share('permission', 'null');
        }
    }
}
