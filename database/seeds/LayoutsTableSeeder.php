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
			$u->segments()->attach(Segment::inRandomOrder()->limit(3)->pluck('id'));
		});
	}
}
