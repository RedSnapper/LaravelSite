<?php

namespace App\Providers;

use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

		foreach ($this->getActivities() as $activity) {
			$gate->define($activity->name,function($user) use($activity){
				return $user->hasRole($activity->roles);
			});
		}


    }

	protected function getActivities() {
		return Activity::with('roles')->get();
	}

}
