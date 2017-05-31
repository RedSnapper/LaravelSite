<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

class CreateLayoutsTable extends Migration {

	private $cols = ['name','category_id'];
	private $data = [
		['Home Page',0],
		['Landing Page',0],
		['Article',0],
		['Search',0]
	];
	private $cats = ['General Purpose'];

	public function up() {
		Schema::create('layouts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');

			$table->timestamps();
		});

		$this->populate('layouts');
	}

	public function populate(string $table = "") {
		$section = strtoupper($table);
		$root = Category::root();
		$root->compose($root,["§$section" => $this->cats]); //§ means 'section' in compose
		$cats = Category::section($section)->first()->descendants(false)->pluck('id');

		$records = [];
		foreach ($this->data as $record) {
			$record[1] = $cats[$record[1]];
			array_push($records, array_combine($this->cols, $record));
		}
		DB::table($table)->insert($records);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('layouts', function (Blueprint $table) {
			$table->dropForeign('layouts_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::dropIfExists('layouts');
	}
}




