<?php

use App\Models\Activity;
use App\Models\Category;

class ActivitiesTableSeeder extends BaseTableSeeder {
	public function run() {

		//Users
		$category = $this->getCategory('Users');
		$this->create('USER_ACCESS','User  management access',$category);
		$this->create('USER_CREATE','User creation',$category);
		$this->create('USER_MODIFY','User modification',$category);
		$this->create('USER_SHOW','User show details',$category);

		//Layouts
		$category = $this->getCategory('Layouts');
		$this->create('LAYOUT_ACCESS','Layouts navigation',$category);
		$this->create('LAYOUT_CREATE','Layouts creation',$category);
		$this->create('LAYOUT_MODIFY','Layouts modification',$category);

		//Roles
		$category = $this->getCategory('Roles');
		$this->create('ACCESS_CONTROL','Eligible to reach access control',$category);
		$this->create('ROLE_ACCESS','Roles management access',$category);
		$this->create('ROLE_MODIFY','Roles modification',$category);

		//Activities
		$category = $this->getCategory('Activities');
		$this->create('ACTIVITY_ACCESS','Activities management access',$category);
		$this->create('ACTIVITY_MODIFY','Activities modification',$category);

		//Media
		$category = $this->getCategory('Media');
		$this->create('MEDIA_ACCESS','Media  management access',$category);
		$this->create('MEDIA_CREATE','Media creation',$category);
		$this->create('MEDIA_MODIFY','Media modification',$category);

		//Teams
		$category = $this->getCategory('Teams');
		$this->create('TEAM_ACCESS','Team management access',$category);
		$this->create('TEAM_MODIFY','Team modification',$category);

		//Segments
		$category = $this->getCategory('Segments');
		$this->create('SEGMENT_ACCESS','Segment management access',$category);
		$this->create('SEGMENT_MODIFY','Segment modification',$category);

	}

	protected function getCategory(string $name){
		return Category::reference($name,'ACTIVITIES')->first()->id;
	}

	protected function create(string $name,string $label,int $category_id){
		return factory(Activity::class)->create(compact('name','label','category_id'));
	}

}

