<?php

use Illuminate\Database\Seeder;
use App\Models\Layout;
use App\Models\Segment;

class LayoutsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$faker = Faker\Factory::create();
		factory(Layout::class,5)->create()->each(function ($u) use ($faker) {
			factory(Segment::class,4)->create()->each(function ($v) use ($faker,$u)  {
				$u->mm()->save($v,['syntax'=>$faker->colorName]);
			});
		});
	}
}
