<?php

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Category;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 10:52
 */
class ActivitiesTableSeeder extends Seeder {
	public function run() {
		$devCategory = Category::reference('Activities')->first()->id;
		factory(Activity::class,1)->create(['name'=>'LAYOUT_NAV','label'=>'Layouts navigation','category_id'=> $devCategory]);
		factory(Activity::class,1)->create(['name'=>'ROLE_NAV','label'=>'Roles navigation','category_id'=> $devCategory]);
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_NAV','label'=>'Activities navigation','category_id'=> $devCategory]);
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_INDEX','label'=>'Activities index access','category_id'=> $devCategory]);
//		factory(Activity::class,3)->create();
	}

}

