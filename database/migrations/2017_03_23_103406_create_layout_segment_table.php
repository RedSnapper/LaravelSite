<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayoutSegmentTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('layout_segment', function (Blueprint $table) {
			$table->integer('layout_id')->unsigned()->index();
			$table->integer('segment_id')->unsigned()->index();
			$table->string('syntax')->nullable();

			$table->foreign('layout_id')
			  ->references('id')
			  ->on('layouts')
			  ->onDelete('cascade');

			$table->foreign('segment_id')
			  ->references('id')
			  ->on('segments')
			  ->onDelete('cascade');
			$table->primary(['layout_id', 'segment_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('layout_segment',function (Blueprint $table) {
			$table->dropForeign('layout_segment_layout_id_foreign');
			$table->dropForeign('layout_segment_segment_id_foreign');
		});

		Schema::dropIfExists('layout_segment');

	}
}
