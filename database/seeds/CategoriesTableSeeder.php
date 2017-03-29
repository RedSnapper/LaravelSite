<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 10:33
 */
class CategoriesTableSeeder extends Seeder  {
//	private $finalSize = 60;
	private $nodeCount = 1;
	private $faker;

	public function run() {
		$this->faker = Faker\Factory::create();

		//Do ROOT node first.
		factory(Category::class,1)->create(['id'=>1,'tw'=>1,'sz'=>1,'pa'=>null,'name'=>'ROOT']);
		$this->addGroup('MEDIA',10);
		$this->addGroup('TEAMS',10);
		$this->addGroup('ROLES',10);
		$this->addGroup('SEGMENTS',10);
		$this->addGroup('LAYOUTS',3);
	}

	private function addGroup($name,$size) {
		factory(Category::class,1)->create(['pa'=>1,'name'=>$name]);
		$this->nodeCount++;
		$branchRoot = $this->nodeCount;
		$branchSize = $branchRoot + $size;
//do max 3 branchGroups.
		$branchGroups = min($size,3);
		for($i = 0; $i < $branchGroups; $i++) {
			factory(Category::class,1)->create(['pa' => $branchRoot]);
			$this->nodeCount++;
		}
//add the rest randomly.
		while($this->nodeCount < $branchSize) {
			factory(Category::class,1)->create(['pa' => mt_rand($branchRoot,$this->nodeCount)]);
			$this->nodeCount++;
		}
	}
}