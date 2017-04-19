<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('media', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned();
			$table->integer('team_id')->unsigned();
			$table->string('name')->unique();
			$table->string('path');
			$table->string('mime');
			$table->string('filename');
			$table->integer('size');
			$table->timestamps();

			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');
			$table->foreign('team_id')->references('id')->on('teams')
				->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('media', function (Blueprint $table) {
			$table->dropForeign('media_category_id_foreign');
			$table->dropForeign('media_team_id_foreign');
		});

		Schema::dropIfExists('media');
	}
}
