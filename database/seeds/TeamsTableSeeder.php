<?php

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Team;


class TeamsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run() {
		$devCategory = Category::reference('Teams')->first()->id;
		factory(Team::class,1,['name'=>'Pre-Production','category_id'=> $devCategory])->create();
		factory(Team::class,1,['name'=>'Post-Production','category_id'=> $devCategory])->create();
	}

}
