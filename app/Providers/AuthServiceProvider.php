<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Team;
use App\Policies\CategoryPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
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
			//won't work unless we are happy to use 'can::teamActivityName(user,team)
			//if(Schema::hasTable('activity_role_teams')) {
			//	$gate->define('team' . $activity->name, function ($user,$team) use ($activity) {
			//		return $user->hasTeam($activity->teams);
			//	});
			//}
		}
	}

	protected function getActivities() {
		return Activity::with('roles')->get();
	}

}
