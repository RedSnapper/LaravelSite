<?php

use Illuminate\Database\Seeder;

class LayoutsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(\App\Layout::class,20)->create()->each(function ($u) {
//			$u->segment()->save(factory(\App\Segment::class)->make());
		});
	}
}
