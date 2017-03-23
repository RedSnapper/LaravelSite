<?php

use Illuminate\Database\Seeder;

class LayoutsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$faker = Faker\Factory::create();
		factory(\App\Layout::class,10)->create()->each(function ($u) use ($faker) {
			factory(\App\Segment::class,4)->create()->each(function ($v) use ($faker,$u)  {
				$u->mm()->save($v,['syntax'=>$faker->colorName]);
			});
		});
	}
}
