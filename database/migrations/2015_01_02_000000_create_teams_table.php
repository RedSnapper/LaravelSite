<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

class CreateTeamsTable extends Migration {

	//'§TEAMS'=>['Organisations','Agencies','Other'],
	private $cols = ['name','category_id'];
	private $data = [
		['Otsuka US',0],
		['Red Snapper',1],
		['Demonstration',2],
	];


	public function up() {
		Schema::create('teams', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('category_id')->unsigned()->nullable();
			$table->timestamps();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict'); //was set null. should be either restrict or cascade.
		});

		$this->populate('teams');
	}

	public function populate(string $table) {
		$cats=[];
		array_push($cats,Category::reference('Organisations','TEAMS')->first()->id);
		array_push($cats,Category::reference('Agencies','TEAMS')->first()->id);
		array_push($cats,Category::reference('Other','TEAMS')->first()->id);
		$records = [];
		foreach ($this->data as $record) {
			$record[1] = $cats[$record[1]];
			array_push($records, array_combine($this->cols, $record));
		}
		DB::table($table)->insert($records);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('teams', function (Blueprint $table) {
			$table->dropForeign('teams_category_id_foreign');
			$table->dropColumn(['category_id']);
		});

		Schema::dropIfExists('teams');
	}
}


