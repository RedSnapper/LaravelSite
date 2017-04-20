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
		factory(Activity::class,1)->create(['name'=>'USER_ACCESS','label'=>'User  management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_CREATE','label'=>'User creation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_MODIFY','label'=>'User modification','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'USER_SHOW','label'=>'User show details','category_id'=> $category]);
		//Layouts
		$category = Category::reference('Layouts','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'LAYOUT_ACCESS','label'=>'Layouts navigation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'LAYOUT_CREATE','label'=>'Layouts creation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'LAYOUT_MODIFY','label'=>'Layouts modification','category_id'=> $category]);
		//Roles
		$category = Category::reference('Roles','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'ACCESS_CONTROL','label'=>'Eligible to reach access control.','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'ROLE_ACCESS','label'=>'Roles  management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'ROLE_MODIFY','label'=>'Roles modification','category_id'=> $category]);
		//Activities
		$category = Category::reference('Activities','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_ACCESS','label'=>'Activities  management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'ACTIVITY_MODIFY','label'=>'Activities modification','category_id'=> $category]);
		//Media
		$category = Category::reference('Media','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'MEDIA_ACCESS','label'=>'Media  management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'MEDIA_CREATE','label'=>'Media creation','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'MEDIA_MODIFY','label'=>'Media modification','category_id'=> $category]);
		//Teams
		$category = Category::reference('Teams','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'TEAM_ACCESS','label'=>'Team  management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'TEAM_MODIFY','label'=>'Team modification','category_id'=> $category]);
		//Segments
		$category = Category::reference('Segments','ACTIVITIES')->first()->id;
		factory(Activity::class,1)->create(['name'=>'SEGMENT_ACCESS','label'=>'Segment management access','category_id'=> $category]);
		factory(Activity::class,1)->create(['name'=>'SEGMENT_MODIFY','label'=>'Segment modification','category_id'=> $category]);
	}

}

