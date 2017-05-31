<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Activity;
use App\Policies\Helpers\UserPolicy;

class CreateActivityRoleTable extends Migration {


//['Super User',false,0],
//['User',false,0],

//['ACCESS_CONTROL','Eligible to reach access control',0],
//['USER_ACCESS','User management access',0],
//['USER_MODIFY','User modification',0],
//['USER_SELF_MODIFY','Own Profile Editing',0],
//['USER_SHOW','User show details',0],
//['EDIT_CONFIG','Editorial configuration access',1],


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

		//Populate activity_role
		$su = Role::where('name','Super User')->first();
		foreach(Activity::all() as $activity) {
			$su->givePermissionTo($activity);
		}
		$user = Role::where('name','User')->first();
		$activity = Activity::where('name','USER_SELF_MODIFY')->first();
		$user->givePermissionTo($activity);


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
