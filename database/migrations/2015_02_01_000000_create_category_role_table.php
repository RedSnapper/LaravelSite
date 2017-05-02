<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Policies\Helpers\UserPolicy;

class CreateCategoryRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('category_role', function (Blueprint $table) {
			$table->integer('category_id')->unsigned();
			$table->integer('role_id')->unsigned();
			$table->unsignedTinyInteger('modify')->default(UserPolicy::INHERITING);

			$table->foreign('category_id')
			  ->references('id')
			  ->on('categories')
			  ->onDelete('cascade');

			$table->foreign('role_id')
			  ->references('id')
			  ->on('roles')
			  ->onDelete('cascade');

			$table->primary(['role_id', 'category_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('category_role', function (Blueprint $table) {
			$table->dropForeign('category_role_category_id_foreign');
			$table->dropForeign('category_role_role_id_foreign');
		});

		Schema::dropIfExists('category_role');
    }
}
