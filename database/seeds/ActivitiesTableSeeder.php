<?php

use Illuminate\Database\Seeder;
use App\Models\Activity;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 10:52
 */
class ActivitiesTableSeeder extends Seeder {
	public function run() {
		factory(Activity::class,40)->create();
	}

}

