<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	use CreatesApplication;

	protected function signIn($user = null) {
		$user = $user ?: create(User::class);

		$role = create(Role::class);
		$user->roles()->save($role);

		$this->actingAs($user);

		return $user;
	}
}
