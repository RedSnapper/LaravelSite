<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CreateCategoriesTable extends Migration {

	//§ means it's a section. Cf. treetrait compose();
	private $data = [
			'§ROLES'=>['General Roles','Team Roles'],
			'§SEGMENTS'=>['General Purpose'],
			'§LAYOUTS'=>['General Purpose'],
			'§ACTIVITIES'=>['Control','Editorial'],
			'§MEDIA'=>['Source','Pre-Production','Post-Production'],
			'§TEAMS'=>['Organisations','Agencies','Other'],
			'§TAGS'=>[
				'Media'=>['Quality','Type','Subject','Features','Mood']
			]
	];

	public function up() {
		Schema::create('categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name',32)->index(); //this should really be a binary field - but 'binary' returns a blob.
			$table->boolean('section')->default(false);
			$table->integer('idx')->unsigned()->index();
			$table->integer('parent')->unsigned()->index()->nullable();
			$table->integer('size')->unsigned()->nullable();
			$table->integer('depth')->unsigned()->nullable();
		});

		Schema::table('categories', function (Blueprint $table) {
			$table->foreign('parent')
				->references('idx')
				->on('categories')
				->onDelete('cascade');
		});

		$this->populate('categories');

	}

	public function populate(string $table) {
		$root = Category::create(['parent'=>null,'name'=>'ROOT','section'=>true]);
		(new Category())->compose($root,$this->data);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		//Self table dependencies
		Schema::table('categories', function (Blueprint $table) {
			$table->dropForeign('categories_parent_foreign');
		});

		Schema::dropIfExists('categories');
	}
}


