<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Team;
use App\Policies\CategoryPolicy;
use App\Policies\Helpers\PolicyData;
use App\Policies\TeamPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
	  Category::class => CategoryPolicy::class,
		Team::class => TeamPolicy::class
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot(GateContract $gate) {
		$this->registerPolicies();
		if ($this->canRegisterGates()) {
			$this->registerGates($gate);
		}

		$this->app->singleton(PolicyData::class, function ($app) {
			return new PolicyData($app->make(Connection::class));
		});
	}

	protected function canRegisterGates(): bool {
		if (app()->runningInConsole() && !app()->environment('production')) {
			return $this->checkActivityTableExists();
		}
		return true;
	}

	protected function checkActivityTableExists() {
		return Schema::hasTable('activity_role');
	}

	protected function registerGates(GateContract $gate) {
		foreach ($this->getActivities() as $activity) {
			$gate->define($activity->name, function ($user) use ($activity) {
				return $user->hasRole($activity->roles);
			});
		}
	}

	protected function getActivities() {
		return Activity::with('roles')->get();
	}

}
