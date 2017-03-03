<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RS\NView\Facades\NView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		NView::share('shared','this is shared with all views');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
