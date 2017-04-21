<?php

use App\Models\Segment;
use Illuminate\Support\Collection;

class SegmentsTableSeeder extends BaseTableSeeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		Collection::times(5, function () {

			$values['category_id'] = $this->getRandomCategory('SEGMENTS');

			return factory(Segment::class)->create($values);
		});

	}

}
