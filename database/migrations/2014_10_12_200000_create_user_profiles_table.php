<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration {

	private $cols = ['user_id', 'telephone'];
	private $data = [
		[1,'020 3409 8901'], //param
		[2,'078 8794 1838'], //ben
		[3,'078 2685 2213'], //eddie
		[4,'078 8794 1835'], //david
		[5,'077 5439 6789'], //ash
		[6,'020 3409 8901'], //jack
		[7,'020 3409 8901'], //simone
	];


	public function up() {
		Schema::create('user_profiles', function (Blueprint $table) {
			$table->integer('user_id')->unsigned()->nullable();
			$table->string('telephone');
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')
				->onDelete('cascade');
		});

		$records = []; foreach ($this->data as $record) {array_push($records, array_combine($this->cols, $record));}
		DB::table('user_profiles')->insert($records);

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('user_profiles',function (Blueprint $table) {
			$table->dropForeign('user_profiles_user_id_foreign');
		});

		Schema::dropIfExists('user_profiles');

	}
}
