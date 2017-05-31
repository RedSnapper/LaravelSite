<?php
namespace Database\Seeds;

use App\Models\Category;
use App\Models\Team;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class TeamsTableSeeder extends BaseTableSeeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run() {
		//$devCategory = Category::reference('Organisations')->first()->id;
		//$this->withJoins(['name'=>'Otsuka Staff','category_id'=> $devCategory]);
		//$this->withJoins(['name'=>'Red Snapper Staff','category_id'=> $devCategory]);
		//$this->withJoins(['name'=>'Freelancers','category_id'=> $devCategory]);
	}

	private function withJoins($values = []) {

		Collection::times(1,function () use ($values) {

			$values['category_id'] = @$values['category_id'] ?? $this->getRandomCategory('TEAMS');
			$team = factory(Team::class)->create($values);

			$roles = Role::teamed()->pluck('id');
			foreach($roles as $role) {
				$team->attachRoleUsers($role,[1,2,3]);
			}

			return $team;
		});


	}


}
