<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityRoleTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('activity_role', function (Blueprint $table) {
			$table->integer('activity_id')->unsigned();
			$table->integer('role_id')->unsigned();

			$table->foreign('activity_id')
			  ->references('id')
			  ->on('activities')
			  ->onDelete('cascade');

			$table->foreign('role_id')
			  ->references('id')
			  ->on('roles')
			  ->onDelete('cascade');

			$table->primary(['role_id', 'activity_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('activity_role', function (Blueprint $table) {
			$table->dropForeign('activity_role_activity_id_foreign');
			$table->dropForeign('activity_role_role_id_foreign');
		});

		Schema::dropIfExists('activity_role');
	}
}
