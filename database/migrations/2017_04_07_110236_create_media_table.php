<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration {

	private function forVersions(bool $mainTable) {
		return
		function(Blueprint $table) use($mainTable) {
			$table->increments('id');
			//The three fields following are implment the 'versionable' trait.
			$table->integer('prev_id')->unsigned()->nullable();
			$table->integer('next_id')->unsigned()->nullable();

			$table->integer('category_id')->unsigned();
			$table->integer('team_id')->unsigned();
			//Adding unique may cause problems on versions (Eg: undo name to a unique).
			if($mainTable) {
				$table->string('name')->unique();
			} else {
				$table->string('name');
			}

			//Actual media payload.
			$table->string('path');
			$table->string('mime');
			$table->string('filename');
			$table->integer('size');
			$table->boolean('is_image');
			$table->longText('properties')->nullable();
			$table->longText('details')->nullable();
			$table->longText('exif')->nullable();
			$table->boolean('has_tn')->default(false);

			$table->timestamps();
			if($mainTable) {
				$table->integer('version')->unsigned()->nullable();
			} else {
				$table->integer('primary')->unsigned();
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
			$table->foreign('prev_id')->references('id')->on('media_versions')->onDelete('restrict');
			$table->foreign('next_id')->references('id')->on('media_versions')->onDelete('restrict');
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('restrict');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->foreign('prev_id')->references('id')->on('media_versions')->onDelete('cascade');
			$table->foreign('next_id')->references('id')->on('media_versions')->onDelete('cascade');
			$table->foreign('primary')->references('id')->on('media')->onDelete('cascade');
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
			$table->dropForeign('media_prev_id_foreign');
			$table->dropForeign('media_next_id_foreign');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->dropForeign('media_versions_prev_id_foreign');
			$table->dropForeign('media_versions_next_id_foreign');
			$table->dropForeign('media_versions_primary_foreign');
		});

		Schema::dropIfExists('media');
		Schema::dropIfExists('media_versions');
	}
}
