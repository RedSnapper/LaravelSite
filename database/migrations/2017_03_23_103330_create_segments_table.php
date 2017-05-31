<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Category;


class CreateSegmentsTable extends Migration {

	private $cols = ['name','category_id','syntax','docs'];
	private $data = [
		['Content',0,'BUILDER','Edited content'],
		['View',0,'XML','Markup stuff'],
		['Control',0,'PHP','Dynamic stuff'],
	];

	public function up() {
		Schema::create('segments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('syntax')->nullable();
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');
			$table->text('docs')->nullable();
			$table->timestamps();
		});

		$this->populate('segments');
	}

	public function populate(string $table = "") {
		$cats=[];
		array_push($cats,Category::reference('General Purpose','SEGMENTS')->first()->id); //General Purpose
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
		Schema::table('segments', function (Blueprint $table) {
			$table->dropForeign('segments_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::dropIfExists('segments');
	}
}



