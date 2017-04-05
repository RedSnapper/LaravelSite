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
	public function run() {
		$devCategory = Category::reference('Roles',false)->first()->id;
		$this->withJoins(1,45,['name'=>'SuperUser','category_id'=> $devCategory]);
		$this->withJoins(1,15,['name'=>'Editor','category_id'=> $devCategory]);
		$this->withJoins(15,6);
	}

	private function withJoins($count,$activities = 5,$values = []) {
		factory(Role::class,$count)->create($values)->each(function ($role) use($activities) {
			$role->activities()->attach(Activity::inRandomOrder()->limit($activities)->pluck('id'));
			$role->users()->attach([1,2]); //Ben n Param
			$role->users()->attach(User::inRandomOrder()->whereNotIn('id',[1,2])->limit(5)->pluck('id'));
		});

	}


}
