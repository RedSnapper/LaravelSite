<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

class CreateRolesTable extends Migration {


	private $cols = ['name','category_id','team_based'];
	private $data = [
		['Super User',0,false],
		['User',0,false],
		['Media Modify',1,true],
		['Media Access',1,true],
	];
	private $cats = ['General Roles','Team Roles'];

	public function up() {
		Schema::create('roles', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->boolean('team_based')->default(false);
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');
			$table->timestamps();
		});

	$this->populate('roles');
	}

	public function populate(string $table) {
		$section = strtoupper($table);
		$root = Category::root();
		$root->compose($root,["§$section" => $this->cats]);
		$cats = Category::section($section)->first()->descendants(false)->pluck('id');

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
		Schema::table('roles', function (Blueprint $table) {
			$table->dropForeign('roles_category_id_foreign');
			$table->dropColumn(['category_id']);
		});
		Schema::dropIfExists('roles');
	}
}
