<?php
namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Faker;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 10:33
 */
class CategoriesTableSeeder extends Seeder  {
	private $nodeCount = 1;
	private $faker;

	public function run() {
		//$this->addGroup('SEGMENTS',['Segments']);
	}

	private function addGroup($name,array $names = []) {
		$size = count($names);
		factory(Category::class,1)->create(['parent'=>1,'name'=>$name,'section'=>true]);
		$this->nodeCount++;
		$branchRoot = $this->nodeCount;
		for($i = 0; $i < $size; $i++) {
			$this->addNode($branchRoot,$names[$i]);
		}
	}

	private function addNode($parent,$name) {
		if(!is_null($name)) {
			factory(Category::class,1)->create(['parent' => $parent,'name' => $name]);
		} else {
			factory(Category::class,1)->create(['parent' => $parent]);
		}
		$this->nodeCount++;
	}
}
