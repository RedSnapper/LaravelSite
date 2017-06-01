<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

class CreateTagsTable extends Migration {

	private $cats = ['Media'=>['Issues','Type','Subject','Features','Mood','Judgement']];

//'TAGS':'Quality','Type','Subject','Features','Mood','Judgement']
	private $cols = ['name','category_id'];
	private $data = [
//Issues
		['Colour cast',0],
		['No Focus',0],
		['Wrong Focus',0],
		['Grainy ISO',0],
		['Camera Shake',0],
		['Unwanted Motion',0],
		['Deep DOF',0],
		['Shallow DOF',0],
		['Over Exposed',0],
		['Over Processed',0],
		['Under Exposed',0],
		['Pixelated',0],
		['Too Small',0],
		['Needs Crop',0],
		['Over Cropped',0],
		['Red Eye',0],
//Type
		['Photograph',1],
		['Painting',1],
		['Illustration',1],
		['Drawing',1],
//Subject
		['Portrait',2],
		['Group',2],
		['Equipment',2],
		['Building',2],
		['Exterior',2],
		['Interior',2],
		['Product',2],
//Features
		['Bokeh',3],
		['Composed',3],
//Moods
		['Humorous',4],
		['Exciting',4],
		['Somber',4],
		['Caring',4],
		['Intelligent',4],
		['Serious',4],
		['Professional',4],
		['Inspired',4],
		['Appealing',4],
//Judgement
		['Excellent',5],
		['Perfect',5],
		['Good',5],
		['Poor',5],
	];


	public function up() {
		Schema::create('tags', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->nullable();
			$table->foreign('category_id')->references('id')->on('categories')
				->onDelete('restrict');
			$table->string('name')->unique();
			$table->integer('moderated')->unsigned()->default(0);
			$table->timestamps();
		});

		$this->populate('tags');
	}

	public function populate(string $table = "") {
		$section = strtoupper($table);
		$root = Category::root();
		$root->compose($root,["§$section" => $this->cats]); //§ means 'section' in compose
		$cats = Category::reference('Media','TAGS')->first()->descendants(false)->pluck('id');
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
		Schema::table('tags', function (Blueprint $table) {
			$table->dropForeign('tags_category_id_foreign');
			$table->dropColumn(['category_id']);
		});

		Schema::dropIfExists('tags');
	}
}
