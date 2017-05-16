<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTagTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('media_tag', function (Blueprint $table) {
			$table->integer('media_id')->unsigned()->index();
			$table->integer('tag_id')->unsigned()->index();

			$table->foreign('media_id')
				->references('id')
				->on('media')
				->onDelete('cascade');

			$table->foreign('tag_id')
				->references('id')
				->on('tags')
				->onDelete('cascade');

			$table->primary(['media_id','tag_id']);

		});

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('media_tag', function (Blueprint $table) {
			$table->dropForeign('media_tag_media_id_foreign');
			$table->dropForeign('media_tag_tag_id_foreign');
		});

		Schema::dropIfExists('media_tag');
	}
}
