<?php
namespace Database\Seeds;

use App\Models\Role;
use App\Models\Category;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Collection;

class RolesTableSeeder extends BaseTableSeeder {

	public function run() {

		$totalActivities = Activity::count();

		$category = Category::reference('General')->first()->id;
		$this->withJoins(1,$totalActivities,['name'=>'SuperUser','category_id'=> $category]);
		$this->withJoins(1,intdiv($totalActivities, 2),['name'=>'Editor','category_id'=> $category]);
		$this->withJoins(1,intdiv($totalActivities, 2),['name'=>'User','category_id'=> $category]);

		$this->giveAccessToAllCategories();

	}

	private function withJoins($count, $activities = 5, $values = []) {

		Collection::times($count, function () use ($values, $activities) {

			$values['category_id'] = @$values['category_id'] ?? $this->getRandomCategory('ROLES');
			$role = factory(Role::class)->create($values);

			$role->activities()->attach(Activity::inRandomOrder()->limit($activities)->pluck('id'));
			$role->users()->attach([1, 2]); //Ben n Param
			$role->users()->attach(User::inRandomOrder()->whereNotIn('id', [1, 2])->limit(4)->pluck('id'));

			return $role;
		});
	}

	//Give superuser access to all categories
	protected function giveAccessToAllCategories(){
		$category = Category::section('ROOT')->first();
		Role::first()->givePermissionToCategory($category);
	}


}
