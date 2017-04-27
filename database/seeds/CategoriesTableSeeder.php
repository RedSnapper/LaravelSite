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
		$this->faker = Faker\Factory::create();

		//Do ROOT node first.
		factory(Category::class,1)->create(['id'=>1,'idx'=>1,'size'=>1,'parent'=>null,'name'=>'ROOT','section'=>true]);
		$this->addGroup('ROLES',3,['Super','Admin','Staff','Public']);
		$this->addGroup('SEGMENTS',6);
		$this->addGroup('LAYOUTS',4);
		$this->addGroup('ACTIVITIES',7,['Activities','Layouts','Media','Roles','Segments','Teams','Users']);
		$this->addGroup('MEDIA',3,['Source','Pre-Production','Post-Production']);
		$this->addGroup('TEAMS',2,['Organisations','Agencies','Other']);
	}

	private function addGroup($name,$size = 3,array $names = []) {
		$size = max($size,count($names));
		factory(Category::class,1)->create(['parent'=>1,'name'=>$name,'section'=>true]);
		$this->nodeCount++;
		$branchRoot = $this->nodeCount;
		$branchSize = $branchRoot + $size; //so this is the number of categories under this branch, excluding local root.
		$name = count($names) == 0 ? ucfirst(strtolower($name)) : $names[0]; //the first name by $names[0] or via groupName.
		$this->addNode($branchRoot,$name);
		$branchNodes = min($size,max(3,count($names) - 1)); //one of the names was used for the first category.
		for($i = 0; $i < $branchNodes; $i++) {
			$this->addNode($branchRoot,@$names[$branchSize - $this->nodeCount]);
		}
//add the rest randomly.
		while($this->nodeCount < $branchSize) {
			$this->addNode(mt_rand($branchRoot,$this->nodeCount),@$names[$branchSize - $this->nodeCount]);
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
