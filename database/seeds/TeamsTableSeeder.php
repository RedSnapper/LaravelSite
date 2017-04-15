<?php

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Team;
use App\Models\Role;
use App\Models\User;

class TeamsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run() {
		$devCategory = Category::reference('Teams')->first()->id;
		$this->withJoins(1,3,['name'=>'Pre-Production','category_id'=> $devCategory]);
		$this->withJoins(1,3,['name'=>'Post-Production','category_id'=> $devCategory]);
		$this->withJoins(5,1);
	}

	private function withJoins($count,$roles = 5,$values = []) {
		factory(Team::class,$count)->create($values)->each(function ($team) use($roles) {
			$roles = Role::inRandomOrder()->limit($roles)->pluck('id');
			foreach($roles as $role) {
				$team->attachRoleUsers($role,[1,2]);
				$team->attachRoleUsers($role,User::inRandomOrder()->whereNotIn('id',[1,2])->limit(4)->pluck('id')->all());
			}
		});

	}


}
