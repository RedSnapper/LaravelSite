<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration {
	public function up() {
		Schema::create('categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name',32)->index(); //this should really be a binary field - but 'binary' returns a blob.
			$table->boolean('section')->default(false);
			$table->integer('idx')->unsigned()->index();
			$table->integer('parent')->unsigned()->index()->nullable();
			$table->integer('size')->unsigned()->nullable();
			$table->integer('depth')->unsigned()->nullable();
		});

		Schema::table('categories', function (Blueprint $table) {
			$table->foreign('parent')
				->references('idx')
				->on('categories')
				->onDelete('cascade'); //onUpdate('cascade') will not work for innodb tables
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		//Self table dependencies
		Schema::table('categories', function (Blueprint $table) {
			$table->dropForeign('categories_parent_foreign');
		});

		Schema::dropIfExists('categories');
	}
}


