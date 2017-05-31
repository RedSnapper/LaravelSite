<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

	private $cols = ['id','name', 'email', 'password', 'remember_token'];
	private $data = [
		[1,'Param', 'param@redsnapper.net', '$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[2,'Ben', 	'ben@redsnapper.net', 	'$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[3,'Eddie', 'eddie@redsnapper.net', '$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[4,'David', 'david@redsnapper.net', '$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[5,'Ash', 	'ash@redsnapper.net', 	'$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[6,'Jack', 	'jack@redsnapper.net', 	'$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
		[7,'Simone','simone@redsnapper.net','$2y$10$7jK/cvA1p7oHHRZNMXkt5uxmXmd518xAcVAwS1MRu2Pyy17FPbN0a', 'HinGAeOnsU'],
	];

	public function up() {
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password');
			$table->rememberToken();
			$table->timestamps();
		});

		$records = []; foreach ($this->data as $record) {array_push($records, array_combine($this->cols, $record));}
		DB::table('users')->insert($records);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('users');
	}
}
