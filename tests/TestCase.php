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
		echo 'P';
		if (!$this->live) {
			parent::setUp();
			$this->artisan('migrate');
			$this->artisan('db:seed');
			$this->live = true;
		}

		$this->beforeApplicationDestroyed(function () {
			if (!$this->dead) {
				$this->artisan('migrate:reset');
				$this->dead = false;
			}
		});
	}

	protected function signIn($user = null) {
		$user = $user ?: User::find(1); //Param
		$this->actingAs($user);
		return $user;
	}

}