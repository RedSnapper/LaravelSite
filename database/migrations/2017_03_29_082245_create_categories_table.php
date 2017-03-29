<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
	public function up()
	{
		Schema::create('categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('tw')->unsigned()->index();
			$table->integer('pa')->unsigned()->index()->nullable();
			$table->integer('sz')->unsigned()->nullable();
			$table->integer('nc')->storedAs("tw+sz")->index();
		});

		Schema::table('categories', function (Blueprint $table) {
			$table->foreign('pa')
				->references('tw')
				->on('categories')
				->onDelete('cascade'); //onUpdate('cascade') will not work for innodb tables
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('categories',function (Blueprint $table) {
			$table->dropForeign('categories_pa_foreign');
		});

		Schema::dropIfExists('categories');
	}
}
