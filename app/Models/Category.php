<?php

namespace App\Models;

use App\Models\Helpers\TreeInterface;
use App\Models\Helpers\TreeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Category extends Model implements TreeInterface {
	use TreeTrait {
		moveInto as t_moveInto;	moveBefore as t_moveBefore;	moveAfter as t_moveAfter;
	}
	protected $guarded  = ['id','size','nextchild','section'];
	public $timestamps = false;
	protected $table="categories";

	public static function branch(string $section = "ROOT") {
		return static::nodeBranch($section, function (Category $category) {
			return Gate::allows('category', $category);
		});
	}

	public function moveInto(int $node){
		return $this->t_moveInto($node, function (Category $category) {
			return Gate::allows('category', $category);
		});
	}

	public function moveBefore(int $node) {
		return $this->t_moveBefore($node, function (Category $category) {
			return Gate::allows('category', $category);
		});
	}

	public function moveAfter(int $node){
		return $this->t_moveAfter($node, function (Category $category) {
			return Gate::allows('category', $category);
		});
	}

	public function activities(){
		return $this->hasMany(Activity::class);
	}
	public function segments(){
		return $this->hasMany(Segment::class);
	}
	public function layouts(){
		return $this->hasMany(Layout::class);
	}
	public function roles(){
		return $this->hasMany(Role::class);
	}

}
