<?php

namespace App\Models;

use App\Models\Helpers\TreeInterface;
use App\Models\Helpers\TreeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Category extends Model implements TreeInterface {

	use TreeTrait;

	protected $guarded = ['id', 'size', 'nextchild', 'section'];
	public $timestamps = false;
	protected $table = "categories";

	public static function branch(string $section = "ROOT") {
		return static::nodeBranch($section, function (Category $category) {
			return Gate::allows('view', $category);
		});
	}

	protected function canUpdate(Category $category){
		return Gate::allows('update', $category);
	}

	public function activities() {
		return $this->hasMany(Activity::class);
	}

	public function segments() {
		return $this->hasMany(Segment::class);
	}

	public function layouts() {
		return $this->hasMany(Layout::class);
	}

	public function roles() {
		return $this->hasMany(Role::class);
	}

}
