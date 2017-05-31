<?php
namespace Database\Seeds;

use App\Models\Activity;
use App\Models\Category;


class ActivitiesTableSeeder extends BaseTableSeeder {
	public function run() {

		////Do NOT add ACCESS/MODIFY to Category controlled objects.
		//
		////User/Access
		//$category = $this->getCategory('Control');
		////General Access Management
		//$this->create('ACCESS_CONTROL','Eligible to reach access control',$category); //All of Roles/Activities/Teams
		//
		//$this->create('USER_ACCESS','User management access',$category);
		//$this->create('USER_MODIFY','User modification',$category);
		//$this->create('USER_SELF_MODIFY','Own Profile Editing',$category);
		//$this->create('USER_SHOW','User show details',$category);
		//
		////Editorial config (layouts, segments, etc).
		//$category = $this->getCategory('Editorial');
		//$this->create('EDIT_CONFIG','Editorial configuration access',$category);

	}

	protected function getCategory(string $name){
		return Category::reference($name,'ACTIVITIES')->first()->id;
	}

	protected function create(string $name,string $label,int $category_id){
		return factory(Activity::class)->create(compact('name','label','category_id'));
	}

}

