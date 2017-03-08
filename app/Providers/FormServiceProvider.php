<?php

namespace App\Providers;

use App\Http\Forms\Form;
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
			$formlet->setURLGenerator($app['url']);
		});

		$this->app->resolving(Form::class, function (Form $formlet, $app) {
			$formlet->setSessionStore($app['session.store']);
			$formlet->setURLGenerator($app['url']);
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
