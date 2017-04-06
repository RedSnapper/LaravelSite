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
			$table->integer('nextchild')->storedAs("`idx`+size")->index();
			$table->integer('depth')->unsigned()->nullable();
		});

		Schema::table('categories', function (Blueprint $table) {
			$table->foreign('parent')
				->references('idx')
				->on('categories')
				->onDelete('cascade'); //onUpdate('cascade') will not work for innodb tables
		});

//		Other table dependencies.
		Schema::table('roles', function (Blueprint $table) {
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('set null');
		});
		Schema::table('layouts', function (Blueprint $table) {
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('set null');
		});
		Schema::table('segments', function (Blueprint $table) {
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('set null');
		});
		Schema::table('activities', function (Blueprint $table) {
			$table->integer('category_id')->unsigned()->nullable();
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
		//Other table dependencies
		Schema::table('roles', function (Blueprint $table) {
			$table->dropForeign('roles_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::table('layouts', function (Blueprint $table) {
			$table->dropForeign('layouts_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::table('segments', function (Blueprint $table) {
			$table->dropForeign('segments_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::table('activities', function (Blueprint $table) {
			$table->dropForeign('activities_category_id_foreign');
			$table->dropColumn(['category_id']);
		});

		//Self table dependencies
		Schema::table('categories', function (Blueprint $table) {
			$table->dropForeign('categories_parent_foreign');
		});

		Schema::dropIfExists('categories');
	}
}


