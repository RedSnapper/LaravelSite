<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
	protected $fillable = [
		'name'
	];

	/**
	 * many-many relations
	 **/

	public function mm() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}
}
