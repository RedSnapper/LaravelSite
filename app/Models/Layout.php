<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
	protected $fillable = [
		'name','category_id'
	];

	public function mm() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}

	public function category() {
		return $this->hasOne(Category::class);
	}

	public function segments() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}
}
