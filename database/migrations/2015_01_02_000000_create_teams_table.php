<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('teams', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('category_id')->unsigned()->nullable();
			$table->timestamps();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('set null');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('teams', function (Blueprint $table) {
			$table->dropForeign('teams_category_id_foreign');
			$table->dropColumn(['category_id']);
		});

		Schema::dropIfExists('teams');
	}
}
