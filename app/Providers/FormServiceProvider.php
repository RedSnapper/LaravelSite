<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Formlets\Formlet;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		$this->app->resolving(Formlet::class, function (Formlet $formlet, $app) {
			$formlet->setSessionStore($app['session.store']);
			$formlet->setRequest($app['request']);
		});
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
