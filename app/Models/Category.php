<?php

namespace App\Models;

use App\Models\Helpers\TreeInterface;
use App\Models\Helpers\TreeTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements TreeInterface {
	use TreeTrait;
	protected $guarded  = ['id','sz','nc'];
	public $timestamps = false;
	protected $table="categories";
}
