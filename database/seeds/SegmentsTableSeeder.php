<?php

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 13:40
 */
use App\Models\Segment;
use Illuminate\Database\Seeder;

class SegmentsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(Segment::class,5)->create();
	}

}
