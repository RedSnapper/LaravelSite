<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->createUsers(1, ['email' => 'param@redsnapper.net', 'name' => 'Param']);
		$this->createUsers(5);
	}

	public function createUsers($users, $attr = []) {
		factory(\App\User::class, $users)->create($attr)->each(function ($u) {
			$u->profile()->save(factory(\App\UserProfile::class)->make());
		});
	}
}
