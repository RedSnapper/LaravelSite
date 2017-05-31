<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\User;

class CreateRoleUserTable extends Migration {

	public function up() {
		Schema::create('role_user', function (Blueprint $table) {
			$table->integer('role_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();

			$table->foreign('user_id')
			  ->references('id')
			  ->on('users')
			  ->onDelete('cascade');

			$table->foreign('role_id')
			  ->references('id')
			  ->on('roles')
			  ->onDelete('cascade');

			$table->primary(['role_id','user_id']);
		});

		//Populate role_user
		$role = Role::where('name','User')->first();
		foreach(User::all() as $user) {
			$role->allowUser($user);
		}
		$su = Role::where('name','Super User')->first();
		foreach(User::where('name','Ben')->orWhere('name','Param')->get() as $user) {
			$su->allowUser($user);
		}


	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('role_user', function (Blueprint $table) {
			$table->dropForeign('role_user_role_id_foreign');
			$table->dropForeign('role_user_user_id_foreign');
		});

		Schema::dropIfExists('role_user');
	}
}
