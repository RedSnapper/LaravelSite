<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	use CreatesApplication;

	private $live = false;
	private $dead = false;

	//Run Database setup.
	protected function setUp() {
		if (!$this->live) {
			parent::setUp();
			$this->artisan('migrate');
			$this->artisan('db:seed');
			$this->live = true;
		}

		$this->beforeApplicationDestroyed(function () {
			if(!$this->dead) {
				$this->artisan('migrate:reset');
				$this->dead = false;
			}
		});
	}


	protected function signIn($user = null) {
		$user = $user ?: create(User::class);

		$this->actingAs($user);
		return $user;
	}
}
