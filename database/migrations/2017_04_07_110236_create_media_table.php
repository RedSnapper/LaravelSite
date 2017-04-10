<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('category_id')->unsigned();
			$table->string('name')->unique();
			$table->string('path');
			$table->string('mime');
			$table->string('filename');
			$table->integer('size');
            $table->timestamps();

			$table->foreign('category_id')->references('id')->on('categories')
			  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    	Schema::table('media', function (Blueprint $table) {
			$table->dropForeign('media_category_id_foreign');
		});

    	Schema::dropIfExists('media');

    }
}
