<?php

use Illuminate\Database\Seeder;
use Database\Seeds\AppDatabaseSeeder;

class DatabaseSeeder extends Seeder {
	/**
	 * @var AppDatabaseSeeder
	 */
	private $appSeeder;

	public function __construct(AppDatabaseSeeder $appSeeder) {
		$this->appSeeder = $appSeeder;
	}

	public function run()  {
		$this->appSeeder->run();
	}
}
