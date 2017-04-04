<?php

namespace App\Providers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;

class FractalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->resolving(ApiController::class, function (ApiController $controller, $app) {

			$manager = new Manager();

			$controller->setManager($manager);
		});
    }
}
