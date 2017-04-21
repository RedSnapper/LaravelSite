<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class BaseTableSeeder extends Seeder{

	protected function getRandomCategory(string $section) {
		return Category::section($section)->first()->descendants(false, false)->inRandomOrder()->first()->id;
	}

}