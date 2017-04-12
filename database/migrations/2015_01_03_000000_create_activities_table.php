<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('activities', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('label')->nullable();
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('set null');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('activities', function (Blueprint $table) {
			$table->dropForeign('activities_category_id_foreign');
			$table->dropColumn(['category_id']);
		});

		Schema::dropIfExists('activities');
	}
}
