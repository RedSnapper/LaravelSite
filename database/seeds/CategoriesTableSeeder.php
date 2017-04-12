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
		factory(Category::class,1)->create(['id'=>1,'idx'=>1,'size'=>1,'parent'=>null,'name'=>'ROOT','section'=>true]);
		$this->addGroup('ROLES',3);
		$this->addGroup('SEGMENTS',6);
		$this->addGroup('LAYOUTS',4);
		$this->addGroup('ACTIVITIES',2);
		$this->addGroup('MEDIA',4);
		$this->addGroup('TEAMS',4);
	}

	private function addGroup($name,$size = 3) {
		factory(Category::class,1)->create(['parent'=>1,'name'=>$name,'section'=>true]);
		$this->nodeCount++;
		$branchRoot = $this->nodeCount;
		$branchSize = $branchRoot + $size;
		factory(Category::class,1)->create(['parent' => $branchRoot,'name' => ucfirst(strtolower($name)) ]);
		$this->nodeCount++;
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
	}
}
