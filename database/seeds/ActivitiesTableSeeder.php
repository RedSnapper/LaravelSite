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
		//Users
		$category = Category::reference('Users','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'USER_NAV','label'=>'User navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_INDEX','label'=>'User index access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_CREATE','label'=>'User creation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_DESTROY','label'=>'User deletion','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_MODIFY','label'=>'User edit/update access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_SHOW','label'=>'User show details','category_id'=> $category]);
		//Layouts
		$category = Category::reference('Layouts','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'LAYOUT_NAV','label'=>'Layouts navigation','category_id'=> $category]);
		//Roles
		$category = Category::reference('Roles','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'ACCESS_CONTROL','label'=>'Eligible to reach access control.','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'ROLE_NAV','label'=>'Roles navigation','category_id'=> $category]);
		//Activities
		$category = Category::reference('Activities','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_NAV','label'=>'Activities navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_INDEX','label'=>'Activities index access','category_id'=> $category]);
		//Media
		$category = Category::reference('Media','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'MEDIA_NAV','label'=>'Media navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'MEDIA_INDEX','label'=>'Media index access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'MEDIA_CREATE','label'=>'Media creation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'MEDIA_EDIT','label'=>'Media edit/update access','category_id'=> $category]);
		//Teams
		$category = Category::reference('Teams','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'TEAM_NAV','label'=>'Team navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'TEAM_INDEX','label'=>'Team index access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'TEAM_EDIT','label'=>'Team edit/update access','category_id'=> $category]);
		//Segments
		$category = Category::reference('Segments','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'SEGMENT_NAV','label'=>'Segment navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'SEGMENT_INDEX','label'=>'Segment index access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'SEGMENT_EDIT','label'=>'Segment edit/update access','category_id'=> $category]);
	}

}

