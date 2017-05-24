<?php

namespace App\Models;

use App\Models\Helpers\TreeInterface;
use App\Models\Helpers\TreeTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements TreeInterface {

	use TreeTrait;

//	protected $guarded = ['id', 'size', 'nextchild', 'section'];
	protected $guarded = ['id', 'size', 'section'];

	public $timestamps = false;
	protected $table = "categories";


	public function activities() {
		return $this->hasMany(Activity::class);
	}

	public function segments() {
		return $this->hasMany(Segment::class);
	}

	public function layouts() {
		return $this->hasMany(Layout::class);
	}

	public function teams() {
		return $this->hasMany(Team::class);
	}

	public function roles() {
		return $this->hasMany(Role::class);
	}

}
