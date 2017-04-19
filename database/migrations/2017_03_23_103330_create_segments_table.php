<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('segments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('syntax')->nullable();
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');
			$table->text('docs');
			$table->timestamps();
		});
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



