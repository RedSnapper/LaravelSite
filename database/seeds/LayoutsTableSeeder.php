<?php
namespace Database\Seeds;

use Illuminate\Support\Collection;
use App\Models\Layout;
use App\Models\Segment;

class LayoutsTableSeeder extends BaseTableSeeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		Collection::times(5, function () {

			$values['category_id'] = $this->getRandomCategory('LAYOUTS');
			$layout = factory(Layout::class)->create($values);
			$layout->segments()->attach(Segment::inRandomOrder()->limit(3)->pluck('id'));

			return $layout;
		});


	}
}
