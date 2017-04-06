<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
	  'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * @var Collection
	 */
	private $categories;

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot(GateContract $gate) {
		$this->registerPolicies();

		$this->categories = collect();

		if($this->canRegisterGates()){
			$this->registerGates($gate);
		}

	}

	protected function canRegisterGates():bool{
		if(app()->runningInConsole() && !app()->environment('production')){
			return $this->checkActivityTableExists();
		}
		return true;
	}

	protected function checkActivityTableExists(){
		return Schema::hasTable('activity_role');
	}

	protected function registerGates(GateContract $gate){
		foreach ($this->getActivities() as $activity) {
			$gate->define($activity->name, function ($user) use ($activity) {
				return $user->hasRole($activity->roles);
			});
		}
		$gate->define('category', function (User $user,Category $category)  {
			if(!$this->categories->has($user->id)) {
				$this->categories->put($user->id,$this->loadCategories($user->id));
			}
			return $this->categories->get($user->id)->contains($category->id);
		});
	}

	protected function getActivities() {
		return Activity::with('roles')->get();
	}

	protected function loadCategories(int $user) : Collection {

		//select b.id from categories b,categories c,category_role cr,role_user ru where ru.user_id=1 and cr.role_id=ru.role_id and b.idx < c.nextchild and b.idx >= c.idx and c.id=cr.category_id group by b.id;

		$query = DB::table('categories as self')->select('self.id')
		  ->join('categories', function ($join) {
			  $join->on('self.idx', '<', 'categories.nextchild')->on('self.idx' ,'>=','categories.idx');
		  })
		  ->join('category_role','categories.id','category_role.category_id')
		  ->join('role_user','category_role.role_id','role_user.role_id')
		  ->where('role_user.user_id',$user)
		  ->groupBy('self.id');

		return $query->pluck('id');
	}

}
