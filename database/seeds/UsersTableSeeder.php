<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UsersTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->createUsers(1, ['email' => 'param@redsnapper.net', 'name' => 'Param']);
		$this->createUsers(1, ['email' => 'ben@redsnapper.net', 'name' => 'Ben']);
		$this->createUsers(7);
	}

	public function createUsers($users, $attr = []) {
		factory(User::class, $users)->create($attr)->each(function ($u) {
			$u->profile()->save(factory(UserProfile::class)->make());
		});

	}
}
