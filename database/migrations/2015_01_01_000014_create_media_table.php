<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CreateMediaTable extends Migration {

	private $cats = 	['Source','Pre-Production','Post-Production'];

/**
+-------------+------------------+------+-----+---------+----------------+
| Field       | Type             | Null | Key | Default | Extra          |
+-------------+------------------+------+-----+---------+----------------+
k id          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
+-------------+------------------+------+-----+---------+----------------+
√ category_id | int(10) unsigned | NO   | MUL | NULL    |                |
√ team_id     | int(10) unsigned | NO   | MUL | NULL    |                |
√ name        | varchar(255)     | NO   | UNI | NULL    |                |
√ filename    | varchar(255)     | NO   |     | NULL    |                |
√ rating      | int(11)          | NO   |     | 0       |                |
√ license_ta  | text             | YES  |     | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+
d path        | varchar(255)     | NO   |     | NULL    |                |
d mime        | varchar(255)     | NO   |     | NULL    |                |
d size        | int(11)          | NO   |     | NULL    |                |
d is_image    | tinyint(1)       | NO   |     | NULL    |                |
d properties  | longtext         | YES  |     | NULL    |                |
d details     | longtext         | YES  |     | NULL    |                |
d exif        | longtext         | YES  |     | NULL    |                |
d has_tn      | tinyint(1)       | NO   |     | 0       |                |
d created_at  | timestamp        | YES  |     | NULL    |                |
d updated_at  | timestamp        | YES  |     | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+
v version     | int(10) unsigned | YES  |     | NULL    |                |
v prev_id     | int(10) unsigned | YES  | MUL | NULL    |                |
v next_id     | int(10) unsigned | YES  | MUL | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+
**/
	private function forVersions(bool $mainTable) {
		return
		function(Blueprint $table) use($mainTable) {
			$table->increments('id');


			$table->integer('category_id')->unsigned();
			$table->integer('team_id')->unsigned();
			//TODO: Adding unique may cause problems on versions (Eg: undo name to a unique).
			if($mainTable) {
				$table->string('name')->unique();
			} else {
				$table->string('name');
			}
			$table->string('filename');
			$table->integer('rating')->nullable()->default(0);
			$table->text('license_ta')->nullable();

			//Derived (non-editable) fields
			$table->timestamps();
			$table->string('path');
			$table->string('mime');
			$table->integer('size');
			$table->boolean('is_image');
			$table->longText('properties')->nullable();
			$table->longText('details')->nullable();
			$table->longText('exif')->nullable();
			$table->boolean('has_tn')->default(false);

			//The following fields implement the 'versionable' trait.
			if($mainTable) {
				$table->integer('version')->unsigned()->nullable();
			} else {
				$table->integer('primary')->unsigned();
			}
			$table->integer('prev_id')->unsigned()->nullable();
			$table->integer('next_id')->unsigned()->nullable();
		};
	}


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('media',$this->forVersions(true));
		Schema::create('media_versions',$this->forVersions(false));

		Schema::table('media', function (Blueprint $table) {
			$table->foreign('prev_id')->references('id')->on('media_versions')->onDelete('restrict');
			$table->foreign('next_id')->references('id')->on('media_versions')->onDelete('restrict');
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('restrict');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->foreign('prev_id')->references('id')->on('media_versions')->onDelete('cascade');
			$table->foreign('next_id')->references('id')->on('media_versions')->onDelete('cascade');
			$table->foreign('primary')->references('id')->on('media')->onDelete('cascade');
		});

		$this->populate('media');

	}

	public function populate(string $table) {
		$section = strtoupper($table);
		$root = Category::root();
		$root->compose($root,["§$section" => $this->cats]); //§ means 'section' in compose
//		$cats = Category::section($section)->first()->descendants(false)->pluck('id');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('media', function (Blueprint $table) {
			$table->dropForeign('media_category_id_foreign');
			$table->dropForeign('media_team_id_foreign');
			$table->dropForeign('media_prev_id_foreign');
			$table->dropForeign('media_next_id_foreign');
		});

		Schema::table('media_versions', function (Blueprint $table) {
			$table->dropForeign('media_versions_prev_id_foreign');
			$table->dropForeign('media_versions_next_id_foreign');
			$table->dropForeign('media_versions_primary_foreign');
		});

		Schema::dropIfExists('media');
		Schema::dropIfExists('media_versions');
	}
}
