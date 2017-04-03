<?php

namespace App\Models;

use App\Events\CategoryCreated;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
	use TreeModelTrait;
	protected $guarded  = ['id','sz','nc'];
	public $timestamps = false;
	protected $table="categories";

	protected $events = [
		'created' => CategoryCreated::class
	];
	

}
