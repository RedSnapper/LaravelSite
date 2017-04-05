<?php

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Layout;
use App\Models\Role;
use App\Models\Segment;
use App\Models\Activity;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 10:33
 */
class CategoriesTableSeeder extends Seeder  {
	private $nodeCount = 1;
	private $faker;

	public function run() {
		$this->faker = Faker\Factory::create();

		//Do ROOT node first.
		factory(Category::class,1)->create(['id'=>1,'idx'=>1,'size'=>1,'parent'=>null,'name'=>'ROOT']);
		$this->addGroup('ROLES',Role::class,12);
		$this->addGroup('SEGMENTS',Segment::class,12);
		$this->addGroup('LAYOUTS',Layout::class,12);
		$this->addGroup('ACTIVITIES',Activity::class,12);
	}

	private function addGroup($name,$modelClass = null,$size = 3) {
		factory(Category::class,1)->create(['parent'=>1,'name'=>$name]);
		$this->nodeCount++;
		$branchRoot = $this->nodeCount;
		$branchSize = $branchRoot + $size;
//do max 3 branchGroups.
		$branchGroups = min($size,3);
		for($i = 0; $i < $branchGroups; $i++) {
			factory(Category::class,1)->create(['parent' => $branchRoot]);
			$this->nodeCount++;
		}
//add the rest randomly.
		while($this->nodeCount < $branchSize) {
			factory(Category::class,1)->create(['parent' => mt_rand($branchRoot,$this->nodeCount)]);
			$this->nodeCount++;
		}

		if (isset($modelClass)) {
			$tree  = new Category;
			$model = new $modelClass;
			$records = $model->get();
			foreach ($records as $record) {
				$record->category_id = $tree->index(mt_rand($branchRoot+1,$this->nodeCount))->first()->id;
				$record->save();
			}
		}
	}
}
