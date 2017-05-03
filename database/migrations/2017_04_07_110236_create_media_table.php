<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration {

	private function forVersions(bool $mainTable) {
		return
		function(Blueprint $table) use($mainTable) {
			$table->increments('id');
			$table->integer('version_id')->unsigned()->nullable();
			$table->integer('category_id')->unsigned();
			$table->integer('team_id')->unsigned();
			$table->string('name')->unique();
			$table->string('path');
			$table->string('mime');
			$table->string('filename');
			$table->integer('size');
			$table->timestamps();
			if(!$mainTable) {
				$table->integer('master_id')->unsigned();
			}
		};
	}

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('media',$this->forVersions(true));
		Schema::create('media_versions',$this->forVersions(false));

		Schema::table('media', function (Blueprint $table) {
			$table->foreign('version_id')->references('id')->on('media_versions')->onDelete('restrict');

			$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('restrict');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->foreign('version_id')->references('id')->on('media_versions')->onDelete('cascade');
			$table->foreign('master_id')->references('id')->on('media')->onDelete('cascade');

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
			$table->dropForeign('media_version_id_foreign');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->dropForeign('media_versions_version_id_foreign');
			$table->dropForeign('media_versions_master_id_foreign');
		});

		Schema::dropIfExists('media');
		Schema::dropIfExists('media_versions');
	}
}
