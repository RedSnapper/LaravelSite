<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('addresses', function (Blueprint $table) {
			$table->increments('id');
			$table->string('street');
			$table->string('city');
			$table->string('postcode');
			$table->timestamps();
		});

		Schema::table('user_profiles', function(Blueprint $table) {
			$table->integer('billing_id')->unsigned()->nullable();
			$table->integer('delivery_id')->unsigned()->nullable();
			$table->foreign('billing_id')->references('id')->on('addresses')
				->onDelete('cascade');
			$table->foreign('delivery_id')->references('id')->on('addresses')
				->onDelete('cascade');
		});

		$this->populate();
	}

	public function populate(string $table = "") {
		//currently unused.
	}
		/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('user_profiles',function (Blueprint $table) {
			$table->dropForeign('user_profiles_billing_id_foreign');
			$table->dropForeign('user_profiles_delivery_id_foreign');
		});


		Schema::dropIfExists('addresses');

	}
}
