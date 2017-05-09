<?php
namespace Database\Seeds;

use App\Models\Role;
use App\Models\Category;
use App\Models\Activity;
use App\Models\User;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Support\Collection;

class RolesTableSeeder extends BaseTableSeeder {

	public function run() {

		$totalActivities = Activity::count();
		$category = Category::reference('General Roles')->first()->id;
		$this->withJoins(1,$totalActivities,['name'=>'SuperUser','team_based'=>false,'category_id'=> $category]);
		$this->withJoins(1,0,['name'=>'User','team_based'=>false,'category_id'=> $category]);
		$this->giveAccessToAllCategories();

		$category = Category::reference('Team Roles')->first()->id;
		$mediaCat = Category::section('MEDIA')->first();
		$this->withJoins(1,0,['name'=>'MediaModify','team_based'=>true,'category_id'=> $category],$mediaCat);
	}

	private function withJoins($count, $activities, $values = [],Category $cat = null) {
		Collection::times($count, function () use ($values, $activities,$cat) {
			$values['category_id'] = @$values['category_id'] ?? $this->getRandomCategory('ROLES');
			$role = factory(Role::class)->create($values);
			if(! is_null($cat)) {
				$role->givePermissionToCategory($cat,UserPolicy::CAN_MODIFY);
				$role->users()->attach([1, 2]); //Ben n Param
			} else {
				$role->activities()->attach(Activity::inRandomOrder()->limit($activities)->pluck('id'));
				$role->users()->attach([1, 2]); //Ben n Param
				$role->users()->attach(User::inRandomOrder()->whereNotIn('id', [1, 2])->limit(4)->pluck('id'));
			}
			return $role;
		});
	}

	//Give superuser access to all categories
	protected function giveAccessToAllCategories(){
		$category = Category::section('ROOT')->first();
		Role::first()->givePermissionToCategory($category,UserPolicy::CAN_MODIFY);
	}


}
