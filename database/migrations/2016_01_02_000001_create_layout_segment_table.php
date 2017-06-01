<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Layout;
use App\Models\Segment;


class CreateLayoutSegmentTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('layout_segment', function (Blueprint $table) {
			$table->integer('layout_id')->unsigned()->index();
			$table->integer('segment_id')->unsigned()->index();
			$table->string('local_name')->nullable();
			$table->string('tab')->nullable();
			$table->string('syntax')->nullable();

			$table->foreign('layout_id')
			  ->references('id')
			  ->on('layouts')
			  ->onDelete('cascade');

			$table->foreign('segment_id')
			  ->references('id')
			  ->on('segments')
			  ->onDelete('cascade');
			$table->primary(['layout_id', 'segment_id']);
		});

		$this->populate();
	}

	public function populate(string $table = "") {
		$content = Segment::where('name','Content')->first();
		foreach(Layout::all() as $layout) {
			$layout->addSegment($content);
		}
		$control = Segment::where('name','Control')->first();
		Layout::where('name','Search')->first()->addSegment($control);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table('layout_segment',function (Blueprint $table) {
			$table->dropForeign('layout_segment_layout_id_foreign');
			$table->dropForeign('layout_segment_segment_id_foreign');
		});

		Schema::dropIfExists('layout_segment');

	}
}
