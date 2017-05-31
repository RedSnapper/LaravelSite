<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\User;
use App\Models\Team;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 13/04/2017 09:18
 */
class CreateRoleTeamUserTable  extends Migration {
	public function up() {
		Schema::create('role_team_user', function (Blueprint $table) {
			$table->integer('role_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();
			$table->integer('team_id')->unsigned()->index();

			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');

			$table->foreign('team_id')
				->references('id')
				->on('teams')
				->onDelete('cascade');

			$table->foreign('role_id')
				->references('id')
				->on('roles')
				->onDelete('cascade');

			$table->primary(['role_id','team_id','user_id']);

		});


		//populate()
		//all users have access to all teams...
		$role = Role::where('name','Media Access')->first();
		foreach(User::all() as $user) {
			foreach (Team::all() as $team) {
				$role->allowTeamUser($team,$user);
			}
		}

		$role = Role::where('name','Media Modify')->first();
		foreach(User::where('name','Ben')->orWhere('name','Param')->get() as $user) {
			foreach (Team::all() as $team) {
				$role->allowTeamUser($team,$user);
			}
		}

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('role_team_user', function (Blueprint $table) {
			$table->dropForeign('role_team_user_role_id_foreign');
			$table->dropForeign('role_team_user_team_id_foreign');
			$table->dropForeign('role_team_user_user_id_foreign');
		});

		Schema::dropIfExists('role_team_user');
	}

}