<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Category;
use App\Models\Activity;
use App\Models\User;

class RolesTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	//TODO:: add fix for superuser to have root access to categories.
	public function run() {
		$category = Category::reference('Super')->first()->id;
		$this->withJoins(1,12345,['name'=>'SuperUser','category_id'=> $category]);
		$category = Category::reference('Admin')->first()->id;
		$this->withJoins(1,7,['name'=>'Editor','category_id'=> $category]);
		$this->withJoins(3,9);

		$this->giveAccessToAllCategories();

	}

	private function withJoins($count,$activities = 5,$values = []) {
		factory(Role::class,$count)->create($values)->each(function ($role) use($activities) {
			if($activities == 12345) {
				$role->activities()->attach(Activity::all()->pluck('id'));
			} else {
				$role->activities()->attach(Activity::inRandomOrder()->limit($activities)->pluck('id'));
			}
				$role->users()->attach([1,2]); //Ben n Param
				$role->users()->attach(User::inRandomOrder()->whereNotIn('id',[1,2])->limit(4)->pluck('id'));
		});

	}

	//Give superuser access to all categories
	protected function giveAccessToAllCategories(){
		$category = Category::section('ROOT')->first();
		Role::first()->givePermissionToCategory($category);
	}

}
